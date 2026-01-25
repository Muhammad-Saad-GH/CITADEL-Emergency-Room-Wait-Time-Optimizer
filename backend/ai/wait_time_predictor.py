import sys
import pickle
import os

# -------------------------------
# Config
# -------------------------------
MIN_WAIT = 5
MAX_WAIT = 240

MODEL_PATH = os.path.join(
    os.path.dirname(__file__),
    "severity_regression.pkl"
)

# -------------------------------
# Helpers
# -------------------------------
def parse_queue(queue_str):
    if not queue_str:
        return []
    return [int(x) for x in queue_str.split(",") if x.isdigit()]


def load_model():
    with open(MODEL_PATH, "rb") as f:
        return pickle.load(f)


def predict_wait(model, severity):
    pred = model.predict([[severity]])[0]
    return max(0, pred)


# -------------------------------
# CLI ENTRY
# -------------------------------
if __name__ == "__main__":
    """
    Args:
    1) queue severities (e.g. "5,4,3")
    2) patient severity (int)
    """

    if len(sys.argv) != 3:
        print(30)
        sys.exit(0)

    queue_arg = sys.argv[1]
    patient_severity = int(sys.argv[2])

    queue_severities = parse_queue(queue_arg)
    model = load_model()

    # Base wait for this patient
    total_wait = predict_wait(model, patient_severity)

    # Add queue-ahead waits
    for sev in queue_severities:
        total_wait += predict_wait(model, sev)

    total_wait = int(round(total_wait))
    total_wait = max(MIN_WAIT, min(total_wait, MAX_WAIT))

    print(total_wait)
