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

client = genai.Client(api_key=api_key) # <--- NEW CLIENT SETUP

def get_triage_assessment(age, sex, symptoms):
    
    # System Prompt
    system_instruction = f"""
    You are an expert Triage Nurse AI.
    Analyze this patient: {age} years old, {sex}.
    Complaint: {symptoms}

    Task:
    1. Assign an Emergency Severity Index (ESI) level (1-5).
    2. Provide 1-sentence medical reasoning.

    Output ONLY valid JSON:
    {{
        "severity_score": int,
        "medical_reasoning": "string"
    }}
    """

    try:
        # NEW CALL STRUCTURE
        response = client.models.generate_content(
            model="gemini-2.0-flash",
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
    p_age = sys.argv[1] if len(sys.argv) > 1 else "Unknown"
    p_sex = sys.argv[2] if len(sys.argv) > 2 else "Unknown"
    p_symptoms = sys.argv[3] if len(sys.argv) > 3 else "No symptoms provided"

    result = get_triage_assessment(p_age, p_sex, p_symptoms)
    print(json.dumps(result))