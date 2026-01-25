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
    You are an expert Triage Nurse AI using the Emergency Severity Index (ESI v4).

    INPUT DATA:
    "{symptoms}"
    (The input contains a [Patient Context] tag with Age/Sex/Pain and the Patient Complaint. Use BOTH.)

    YOUR CORE DIRECTIVES:

    1. VALIDATE PAIN SCORES (Crucial):
       - Do NOT blindly upgrade risk based on the pain number alone.
       - A "10/10 pain" for a minor injury (e.g., papercut, bruised knee) is ESI 4 or 5.
       - Only upgrade to ESI 2 for pain >7 if the *clinical condition* warrants it (e.g., kidney stone, fracture, acute abdomen).

    2. APPLY BIOLOGICAL RISK FACTORS (Extract from Context):
       - Female + Abdominal/Pelvic Pain: Consider ectopic pregnancy/ovarian torsion (Higher Risk).
       - Male > 50 + Flank/Back Pain: Consider Aortic Aneurysm (Higher Risk).
       - Female > 50 + Upper Back/Jaw Pain: Consider atypical Heart Attack symptoms.
       - Pediatric (< 3 months) + Fever: Immediate High Risk (ESI 2).

    3. STANDARD ESI RULES:
       - ESI 1: Dying/Unstable? (Cardiac arrest, severe respiratory distress).
       - ESI 2: High Risk/Confused/Lethargic? (Stroke, Sepsis, true severe trauma).
       - ESI 3: Needs 2+ Resources? (Labs + IV + CT + X-Ray).
       - ESI 4: Needs 1 Resource? (Stitches, X-Ray only, Med refill).
       - ESI 5: Needs 0 Resources? (Exam only).

    Task:
    1. Determine ESI Level (1-5).
    2. Provide 1-sentence medical reasoning. *Explicitly mention if Age/Sex affected the score.*

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