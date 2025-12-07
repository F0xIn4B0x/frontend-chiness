#!/usr/bin/env bash
# install-php-deps.sh
# Detect package manager and install PHP + php-curl + php-mbstring (and some useful extras)
# Usage: sudo ./install-php-deps.sh

set -euo pipefail

PKGS_COMMON=(php php-cli)
PKGS_EXT_DEBIAN=(php-curl php-mbstring php-xml php-zip)
PKGS_EXT_REDHAT=(php php-cli php-curl php-mbstring php-xml php-zip)
PKGS_EXT_FEDORA=(php php-cli php-curl php-mbstring php-xml php-zip)
PKGS_EXT_ARCH=(php)
PKGS_EXT_SUSE=(php php-mbstring php-curl php-xml php-zip)

info() { echo -e "\e[1;34m[INFO]	$*\e[0m"; }
warn() { echo -e "\e[1;33m[WARN]	$*\e[0m"; }
err() { echo -e "\e[1;31m[ERROR]	$*\e[0m"; exit 1; }

if [[ $EUID -ne 0 ]]; then
  SUDO='sudo'
else
  SUDO=''
fi

detect_pm() {
  if command -v apt-get >/dev/null 2>&1; then
    echo apt
  elif command -v dnf >/dev/null 2>&1; then
    echo dnf
  elif command -v yum >/dev/null 2>&1; then
    echo yum
  elif command -v pacman >/dev/null 2>&1; then
    echo pacman
  elif command -v zypper >/dev/null 2>&1; then
    echo zypper
  else
    echo unknown
  fi
}

PM=$(detect_pm)
info "Detected package manager: $PM"

case "$PM" in
  apt)
    ${SUDO} apt-get update
    info "Installing PHP and extensions via apt"
    ${SUDO} apt-get install -y "${PKGS_COMMON[@]}" "${PKGS_EXT_DEBIAN[@]}"
    ;;
  dnf)
    info "Installing PHP and extensions via dnf"
    ${SUDO} dnf install -y "${PKGS_EXT_FEDORA[@]}"
    ;;
  yum)
    info "Installing PHP and extensions via yum"
    ${SUDO} yum install -y "${PKGS_EXT_REDHAT[@]}"
    ;;
  pacman)
    info "Installing PHP via pacman"
    ${SUDO} pacman -Syu --noconfirm
    # Arch's php package usually bundles extensions; try installing php first
    ${SUDO} pacman -S --noconfirm ${PKGS_EXT_ARCH[*]}
    ;;
  zypper)
    info "Installing PHP and extensions via zypper"
    ${SUDO} zypper refresh
    ${SUDO} zypper install -y ${PKGS_EXT_SUSE[*]}
    ;;
  *)
    err "Unsupported distribution / package manager. Please install php, php-curl and php-mbstring manually."
    ;;
esac

# Try to enable modules on Debian derivatives
if command -v phpenmod >/dev/null 2>&1; then
  info "Running phpenmod for common extensions (mbstring, curl) if available"
  ${SUDO} phpenmod mbstring || true
  ${SUDO} phpenmod curl || true
fi

# Restart php-fpm if present
if systemctl list-units --type=service --all | grep -qi "php.*fpm"; then
  info "Restarting php-fpm service(s)"
  for svc in $(systemctl list-units --type=service --all --no-legend | awk '{print $1}' | grep -Ei 'php.*fpm' || true); do
    ${SUDO} systemctl restart "$svc" || warn "Failed restarting $svc"
  done
fi

# Restart apache2 if present
if systemctl list-units --type=service --all | grep -qi "apache2"; then
  info "Restarting apache2"
  ${SUDO} systemctl restart apache2 || warn "Failed restarting apache2"
fi

if systemctl list-units --type=service --all | grep -qi "httpd"; then
  info "Restarting httpd"
  ${SUDO} systemctl restart httpd || warn "Failed restarting httpd"
fi

# Verify installed and enabled
info "Verifying installed PHP extensions"
PHP_FOUND=0
if command -v php >/dev/null 2>&1; then
  PHP_FOUND=1
  php -v | head -n1
  for ext in mbstring curl; do
    if php -r "exit(extension_loaded('$ext')?0:1);" 2>/dev/null; then
      echo "  - $ext: OK"
    else
      echo "  - $ext: MISSING"
    fi
  done
else
  warn "PHP binary not found in PATH"
fi

info "Done. If extensions show as MISSING, try checking your distribution's package names or enable them and restart your PHP service." 

exit 0
