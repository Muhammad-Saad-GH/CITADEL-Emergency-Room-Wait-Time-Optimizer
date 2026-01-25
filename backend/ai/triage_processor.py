# backend/ai/triage_processor.py
import sys
import json
import os
from google import genai
from google.genai import types 
from dotenv import load_dotenv

load_dotenv()

# Setup Client
api_key = os.getenv("GEMINI_API_KEY") 
client = genai.Client(api_key=api_key)

# UPDATED: Only accepts one argument now
def get_triage_assessment(symptoms):
    
    # System Prompt: Simplified to focus ONLY on the complaint
    system_instruction = f"""
    You are an expert Triage Nurse AI using the ESI v4 algorithm.
    
    INPUT DATA:
    "{symptoms}"
    (Note: The input may start with a [Patient Context] tag. USE THIS DATA to adjust risk.)

    RISK ADJUSTMENT RULES:
    - Age > 65 + Chest Pain/Abdominal Pain -> Upgrade to ESI 2 immediately.
    - Pain Level > 7/10 -> Upgrade to ESI 2 (Severe Pain criteria).
    - Pediatric (< 8 years) + High Fever -> Upgrade to ESI 2 or 3.

    STANDARD ESI RULES:
    STEP 1: Dying? (Cardiac/Resp Arrest) -> ESI 1.
    STEP 2: Should wait? (High Risk, Confused, Severe Pain >7) -> ESI 2.
    STEP 3: Resources? (Many labs/X-rays needed) -> ESI 3.
    STEP 4: One Resource? -> ESI 4.
    STEP 5: No Resources? -> ESI 5.

    Task:
    1. Determine ESI Level (1-5).
    If the symptoms are ambiguous or insufficient to make a safe decision, default to a HIGHER severity (lower number) and add '[UNCERTAIN]' to the start of the reasoning.

    2. Provide 1-sentence medical reasoning (mention Age/Pain if relevant).

    Output ONLY valid JSON:
    {{
        "severity_score": int,
        "medical_reasoning": "string"
    }}
    """

    try:
        response = client.models.generate_content(
            model="gemini-flash-lite-latest",
            contents=system_instruction,
            config=types.GenerateContentConfig(
                response_mime_type="application/json", 
                safety_settings=[ 
                    types.SafetySetting(
                        category="HARM_CATEGORY_DANGEROUS_CONTENT",
                        threshold="BLOCK_NONE"
                    ),
                    types.SafetySetting(
                        category="HARM_CATEGORY_HARASSMENT",
                        threshold="BLOCK_NONE"
                    ),
                ]
            )
        )

        return json.loads(response.text)

    except Exception as e:
        return {
            "severity_score": 3,
            "medical_reasoning": f"AI Error: {str(e)}. Manual Review Required."
        }

if __name__ == "__main__":
    
    p_symptoms = sys.argv[1] if len(sys.argv) > 1 else "No symptoms provided"

    result = get_triage_assessment(p_symptoms)
    
    print(json.dumps(result))