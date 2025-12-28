import pandas as pd

try:
    # Read Sheet 2 (index 1)
    df = pd.read_excel('codigos postales.xlsx', sheet_name=1)
    print("Sheet 2 Columns:")
    print(df.columns.tolist())
    print("\nFirst 5 rows:")
    print(df.head(5))
except Exception as e:
    print(f"Error: {e}")
