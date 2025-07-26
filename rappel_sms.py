import mysql.connector
from datetime import date, timedelta
import os

conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='passer',
    database='hopital'
)
cursor = conn.cursor()

demain = (date.today() + timedelta(days=1)).isoformat()

cursor.execute("SELECT numero, service, date_rdv FROM rendezvous WHERE date_rdv = %s", (demain,))
rdvs = cursor.fetchall()

for numero, service, date_rdv in rdvs:
    msg = f"Bonjour, vous avez un rendez-vous demain en {service} à l'hôpital."
    fichier_msg = f"/var/spool/asterisk/tmp/message_{numero}.txt"

    with open(fichier_msg, "w") as f:
        f.write(msg)

    cmd = f"asterisk -rx \"channel originate local/{numero}@envoi_sms application Playback s\""
    os.system(cmd)

conn.close()
