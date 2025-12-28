import pandas as pd

try:
    df = pd.read_excel('codigos postales.xlsx')
    print("Headers:", df.columns.tolist())
    print("\nFirst 5 rows:")
    print(df[['Municipio', 'Ciudad', 'Zona']].head(5))
    print("\nUnique Cities:")
    print(df['Ciudad'].unique()[:10])
except Exception as e:
    print(e)
