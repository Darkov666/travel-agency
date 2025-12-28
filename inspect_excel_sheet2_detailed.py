import pandas as pd

try:
    # Read Sheet 2 (index 1)
    df = pd.read_excel('codigos postales.xlsx', sheet_name=1)
    print("Sheet 2 Columns:")
    for col in df.columns:
        print(f"'{col}'")
except Exception as e:
    print(f"Error: {e}")
