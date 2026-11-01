"""Genera demo/blueprint.json per WordPress Playground.

Uso: python demo/genera-blueprint.py
Il codice PHP della preparazione sta in demo/prepara-demo.php (più comodo da leggere e
modificare che dentro una stringa JSON).
"""
import json
import os

QUI = os.path.dirname(os.path.abspath(__file__))

with open(os.path.join(QUI, 'prepara-demo.php'), encoding='utf-8') as f:
    prepara = f.read()

blueprint = {
    '$schema': 'https://playground.wordpress.net/blueprint-schema.json',
    'meta': {
        'title': "Valley d'Or Padel – demo dell'area riservata",
        'description': "Tema Valley d'Or con pannello di gestione, tornei e utente Gestore del centro.",
        'author': 'ndoga',
        'categories': ['theme'],
    },
    'landingPage': '/wp-admin/admin.php?page=valleydor',
    'preferredVersions': {'php': '8.3', 'wp': 'latest'},
    'features': {'networking': True},
    'steps': [
        {'step': 'setSiteLanguage', 'language': 'it_IT'},
        {'step': 'setSiteOptions', 'options': {'blogname': "Valley d'Or"}},
        {
            'step': 'installTheme',
            'themeData': {
                'resource': 'git:directory',
                'url': 'https://github.com/ndoga/valleydor',
                'ref': 'main',
                'refType': 'branch',
                'path': 'valleydor-padel',
            },
            'options': {'activate': True, 'targetFolderName': 'valleydor-padel'},
        },
        {'step': 'runPHP', 'code': prepara},
        {'step': 'login', 'username': 'gestore', 'password': 'demo'},
    ],
}

with open(os.path.join(QUI, 'blueprint.json'), 'w', encoding='utf-8', newline='\n') as f:
    json.dump(blueprint, f, ensure_ascii=False, indent='\t')
    f.write('\n')

print('demo/blueprint.json scritto')
