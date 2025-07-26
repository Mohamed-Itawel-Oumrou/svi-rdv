from flask import Flask, request, jsonify
import mysql.connector
from datetime import datetime

app = Flask(__name__)

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'passer',
    'database': 'hopital'
}

@app.route('/rendezvous', methods=['GET'])
def enregistrer_rendezvous():
    numero = request.args.get('callerid', 'inconnu')
    langue = request.args.get('lang')
    service = request.args.get('service')
    date_rdv = request.args.get('date')

    if not langue or not service or not date_rdv:
        return jsonify({'status': 'error', 'message': 'Paramètres manquants'}), 400

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        query = """
            INSERT INTO rendezvous (numero, langue, service, date_rdv)
            VALUES (%s, %s, %s, %s)
        """
        cursor.execute(query, (numero, langue, service, date_rdv))
        conn.commit()
        cursor.close()
        conn.close()

        return jsonify({'status': 'ok', 'message': 'Rendez-vous enregistré'})
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
