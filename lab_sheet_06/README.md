# MCA108P – Lab Sheet 6: Python + MySQL + Apache solutions

One folder per question (`q01_…` to `q21_…`). Shared code lives in `common/` and the schema in `sql/setup.sql`.

## Quick start
```bash
python3 -m venv venv && source venv/bin/activate     # Windows: venv\Scripts\activate
pip install -r requirements.txt
export DB_USER=root DB_PASSWORD=yourpassword          # Windows: set DB_USER=root ...
python q04_create_db_table/create_db_table.py         # creates python_web_db + users
python q05_insert_many/insert_many.py                 # sample data
python q21_summary_reports/reports.py                 # try a report
```

## Running the CGI scripts under Apache
Enable CGI (see Q1), then copy the script **and** the `common/` folder so that `../common` resolves
(e.g. `/var/www/html/lab5/q06_html_response/…` and `/var/www/html/lab5/common/`), or adjust the
`sys.path.insert` line in the script. Make scripts executable (`chmod 755`). Scripts are also runnable
from the command line for testing.

| # | Topic | Main file |
|---|---|---|
| 1 | Apache + Python CGI/WSGI | `q01_apache_python_cgi/` |
| 2 | Install MySQL connector | `q02_install_connector/` |
| 3 | Server version query | `q03_db_version/db_version.py` |
| 4 | Create DB + `users` table | `q04_create_db_table/create_db_table.py` |
| 5 | `executemany()` insert | `q05_insert_many/insert_many.py` |
| 6 | Users as HTML | `q06_html_response/users_html.py` |
| 7 | CGI form → MySQL | `q07_cgi_form_insert/` |
| 8 | Update by ID | `q08_update_user/update_user.py` |
| 9 | Delete + try/except | `q09_delete_user/delete_user.py` |
| 10 | `.htaccess` handlers | `q10_htaccess_handlers/` |
| 11 | Parameterized query | `q11_parameterized_query/search_users.py` |
| 12 | Connection pooling | `q12_connection_pool/connection_pool.py` |
| 13 | Error/access logging | `q13_error_logging/logged_db.py` |
| 14 | CSV bulk insert | `q14_csv_bulk_insert/` |
| 15 | SQLAlchemy ORM | `q15_sqlalchemy_orm/orm_demo.py` |
| 16 | JSON endpoint | `q16_json_endpoint/users_json.py` |
| 17 | DB backup to `.sql` | `q17_db_backup/backup_db.py` |
| 18 | Session handling | `q18_session_handling/` (run `create_demo_user.py` first) |
| 19 | Input validation | `q19_input_validation/validated_submit.py` |
| 20 | venv + Apache | `q20_venv_apache/` |
| 21 | Summary reports | `q21_summary_reports/reports.py` |

Note: Python 3.13 removed the `cgi` module; `common/cgi_utils.py` uses it when available and falls back automatically.
