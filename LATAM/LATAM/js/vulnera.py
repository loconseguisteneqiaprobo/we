import socket
import requests
import nmap

# Función para escanear puertos
def port_scan(target):
    open_ports = []
    print(f"\n[+] Escaneando puertos en {target}...")
    for port in range(1, 1025):  # Escanear puertos del 1 al 1024
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        socket.setdefaulttimeout(1)
        result = sock.connect_ex((target, port))
        if result == 0:
            open_ports.append(port)
        sock.close()
    return open_ports

# Función para hacer brute force de directorios
def dir_scan(url):
    directories = ["admin", "login", "dashboard", "config", "uploads", "images"]
    found_dirs = []
    print(f"\n[+] Escaneando directorios en {url}...")
    for directory in directories:
        full_url = f"{url}/{directory}"
        try:
            response = requests.get(full_url)
            if response.status_code == 200:
                found_dirs.append(full_url)
                print(f"[+] Directorio encontrado: {full_url}")
            else:
                print(f"[-] Directorio no encontrado: {full_url} (Status Code: {response.status_code})")
        except requests.exceptions.RequestException as e:
            print(f"Error al acceder a {full_url}: {e}")
    return found_dirs

# Función para usar nmap y encontrar vulnerabilidades
def vuln_scan(target):
    nm = nmap.PortScanner()
    print(f"\n[+] Escaneando vulnerabilidades en {target}...")
    nm.scan(target, '1-1024', '-sV --script vuln')
    return nm[target]['tcp']

# Función principal
def scan(target):
    # Extraer solo el dominio sin protocolo y ruta
    domain = target.replace("http://", "").replace("https://", "").split('/')[0]

    # Verificar si el dominio puede ser resuelto
    try:
        target_ip = socket.gethostbyname(domain)
    except socket.gaierror:
        print(f"[-] Error: No se puede resolver el dominio {domain}. Verifica la URL.")
        return

    # Escanear directorios
    dir_scan(f"http://{domain}")

    # Escanear puertos
    open_ports = port_scan(target_ip)
    if open_ports:
        print(f"[+] Puertos abiertos: {open_ports}")
    else:
        print("[-] No se encontraron puertos abiertos.")

    # Escanear vulnerabilidades con nmap
    vulnerabilities = vuln_scan(target_ip)
    for port, details in vulnerabilities.items():
        print(f"[+] Puerto {port}: {details['name']} - {details['state']}")

# Ejecutar el escáner
if __name__ == "__main__":
    target_url = "https://latamairtravel.com/Co"
    scan(target_url)
