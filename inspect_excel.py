import pandas as pd

try:
    df = pd.read_excel('codigos postales.xlsx')
    print("Columns found:")
    for col in df.columns:
        print(col)
    print("\nFirst row sample:")
    print(df.head(1))
except ImportError as e:
    print(f"Missing dependency: {e}")
except Exception as e:
    print(f"Error reading file: {e}")
