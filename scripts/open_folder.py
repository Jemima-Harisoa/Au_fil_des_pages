#!/usr/bin/env python3
from flask import Flask, request
import subprocess
import os

app = Flask(__name__)

@app.route('/open-folder')
def open_folder():
    path = request.args.get('path', '')
    if os.path.exists(path) and os.path.isdir(path):
        subprocess.Popen(['xdg-open', path])  # Linux
        # subprocess.Popen(['explorer', path])  # Windows
        # subprocess.Popen(['open', path])      # macOS
        return {'success': True}
    return {'success': False}, 404

if __name__ == '__main__':
    app.run(port=5555)
