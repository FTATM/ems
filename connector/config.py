from dotenv import load_dotenv
import os

load_dotenv()  # โหลด .env

DB_CONFIG = {
    "host": os.getenv("DB_HOST"),
    "port": os.getenv("DB_PORT"),
    "database": os.getenv("DB_NAME"),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASS"),
}
LINE_TOKEN = os.getenv("LINE_CHANNEL_TOKEN")
