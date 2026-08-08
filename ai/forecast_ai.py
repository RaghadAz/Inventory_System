import sys
import json
import numpy as np
from sklearn.linear_model import LinearRegression

sales = [float(x) for x in sys.argv[1:]]

if len(sales) < 2:
    print(json.dumps({
        "prediction": 0,
        "average": 0,
        "message": "Not enough data"
    }))
    sys.exit()

X = np.array(range(len(sales))).reshape(-1, 1)
y = np.array(sales)

model = LinearRegression()

model.fit(X, y)

next_day = np.array([[len(sales)]])
prediction = model.predict(next_day)[0]

average = np.mean(sales)

result = {
    "prediction": round(float(prediction), 2),
    "average": round(float(average), 2),
    "message": "Prediction generated successfully"
}

print(json.dumps(result))
