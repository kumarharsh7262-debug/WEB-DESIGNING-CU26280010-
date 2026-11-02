"""Q2 – Verify the MySQL client libraries are installed in this Python environment."""
import sys

print("Python:", sys.version.split()[0], "|", sys.executable)

for module in ("mysql.connector", "pymysql"):
    try:
        mod = __import__(module, fromlist=["__version__"])
        version = getattr(mod, "__version__", None) or getattr(mod, "version_string", "unknown")
        print(f"[OK]      {module:<16} version {version}")
    except ImportError:
        pkg = "mysql-connector-python" if module.startswith("mysql") else "PyMySQL"
        print(f"[MISSING] {module:<16} -> pip install {pkg}")
