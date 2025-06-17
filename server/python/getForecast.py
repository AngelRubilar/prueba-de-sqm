# %%
import pandas as pd
from datetime import datetime
from prophet import Prophet
import requests
import json
import os

# %%
def forecast_so2(mydata):
    '''
    This function takes in a dataframe (mydata) and returns a dataframe with forecasted values for SO2
    '''
    m = Prophet(changepoint_prior_scale=0.01).fit(mydata.dropna())
    future = m.make_future_dataframe(periods=2*24, freq='15min')
    fcst = m.predict(future)
    fcst['current_time'] = datetime.now()
    fcst = fcst[['ds', 'yhat', 'yhat_lower', 'yhat_upper', 'current_time']]
    merged_df = fcst.merge(mydata, on='ds', how='left')
    return merged_df

# %%
def fetch_data():
    url = "http://localhost:3000/api/datos-PM10"
    try:
        response = requests.get(url, timeout=10)
        if response.status_code == 200:
            return pd.DataFrame(json.loads(response.text))
        else:
            print(f"Failed to fetch data. Status code: {response.status_code}")
            return pd.DataFrame()
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {str(e)}")
        return pd.DataFrame()

# %%
# Crear carpeta databases si no existe
databases_dir = 'databases'
if not os.path.exists(databases_dir):
    os.makedirs(databases_dir)
    print(f"Carpeta {databases_dir} creada")

# %%
df = fetch_data()
print(f"Datos obtenidos: {len(df)} registros")

# %%
file_path = os.path.join(databases_dir, 'realreg.parquet')

if os.path.exists(file_path):
    existing_df = pd.read_parquet(file_path)
    combined_df = pd.concat([existing_df, df]).drop_duplicates(keep='last')
else:
    combined_df = df

combined_df.to_parquet(file_path)
print(f"Archivo guardado: {file_path}")

# %%
combined_df['timestamp'] = pd.to_datetime(combined_df['timestamp']).dt.tz_localize(None)
combined_df['valor'] = pd.to_numeric(combined_df['valor'], errors='coerce')
combined_df = combined_df[['timestamp','station_name','valor']].set_index('timestamp').groupby('station_name').resample('15min').mean().reset_index()

# %%
so2Huara = combined_df.query('station_name=="E6"')[['timestamp','valor']]
so2Huara.columns = ['ds','y']

so2Victoria = combined_df.query('station_name=="E7"')[['timestamp','valor']]
so2Victoria.columns = ['ds','y']

so2Pintados = combined_df.query('station_name=="E8"')[['timestamp','valor']]
so2Pintados.columns = ['ds','y']

# %%
fcst_huara = forecast_so2(so2Huara)
fcst_huara['station'] = 'Huara'

fcst_victoria = forecast_so2(so2Victoria)
fcst_victoria['station'] = 'Victoria'

fcst_pintados = forecast_so2(so2Pintados)
fcst_pintados['station'] = 'Colonia Pintados'

# %%
# Concatenate all forecast dataframes
fcst_db = pd.concat([fcst_huara, fcst_victoria, fcst_pintados], ignore_index=True)

# %%
# Check if the file exists
file_path = os.path.join(databases_dir, 'fcst_db.parquet')
if os.path.exists(file_path):
    # Read the existing file
    existing_fcst = pd.read_parquet(file_path)
    # Concatenate the new forecast with the existing one
    combined_fcst = pd.concat([existing_fcst, fcst_db])
    combined_fcst.to_parquet(file_path)
    # Drop duplicates based on 'ds', keeping the most recent 'current_time'
    combined_fcst = combined_fcst.sort_values(by='current_time').drop_duplicates(subset='ds', keep='last')
else:
    # If the file does not exist, use the new forecast as the combined forecast
    combined_fcst = fcst_db
    combined_fcst.to_parquet(file_path)

print(f"Pronóstico guardado: {file_path}")
print(f"Total de registros de pronóstico: {len(combined_fcst)}")