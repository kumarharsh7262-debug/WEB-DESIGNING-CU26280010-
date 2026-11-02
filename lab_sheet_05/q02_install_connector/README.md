# Q2 – Install the Python MySQL connector

```bash
python3 -m venv venv && source venv/bin/activate     # Windows: venv\Scripts\activate
pip install mysql-connector-python                    # Oracle's official driver
pip install PyMySQL                                   # pure-Python alternative
pip list | grep -i -E "mysql|pymysql"                 # confirm
```
Run `python check_connector.py` – both libraries should print their version.

Note: for the Apache CGI scripts, the packages must be installed for the *same* Python that the
shebang line points to (or for the venv used in Q20).
