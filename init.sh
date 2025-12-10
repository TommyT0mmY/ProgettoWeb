#!/bin/bash

# --- COLOR AND FORMATTING CONFIGURATION ---
# Check if the terminal supports colors
if test -t 1 && command -v tput >/dev/null && test "$(tput colors)" -ge 8; then
    RED='\033[0;31m'
    GREEN='\033[0;32m'
    YELLOW='\033[1;33m'
    BLUE='\033[0;34m'
    NC='\033[0m' # No Color
else
    RED=''; GREEN=''; YELLOW=''; BLUE=''; NC=''
fi

# Log message prefixes
MSG_INFO="${BLUE}[ INFO ]${NC}"
MSG_OK="${GREEN}[  OK  ]${NC}"
MSG_WARN="${YELLOW}[ WARN ]${NC}"
MSG_ERR="${RED}[ FAIL ]${NC}"
MSG_QUEST="${BLUE}[  ?   ]${NC}"

# --- DEPENDENCY CHECK ---
HAS_OPENSSL=false
echo -e "${MSG_INFO} Checking dependencies..."

if command -v openssl &> /dev/null; then
    HAS_OPENSSL=true
    echo -e "${MSG_OK} OpenSSL found."
else
    echo -e "${MSG_WARN} OpenSSL NOT found."
    echo -e "         Passwords will be generated using \$RANDOM."
fi

# --- HELPER FUNCTIONS ---

# Function to generate a random string
get_random_string() {
    if [ "$HAS_OPENSSL" = true ]; then
        openssl rand -base64 32
    else
        # Weak fallback
        echo "weak_$(date +%s)$RANDOM$RANDOM$RANDOM$RANDOM" | base64 | head -c 32
    fi
}

# Function to handle secret creation
# Usage: handle_secret "file_path" "Human Readable Secret Name"
handle_secret() {
    local file_path="$1"
    local secret_name="$2"
    local secret_value=""

    # If the file already exists and is not empty, skip it
    if [ -s "$file_path" ]; then
        echo -e "${MSG_WARN} Secret for ${YELLOW}$secret_name${NC} already exists. Skipping."
        return
    fi

    # User Input (Silent for passwords)
    echo -ne "${MSG_QUEST} Enter ${YELLOW}$secret_name${NC} (Random value if empty): "
    read -s input_val
    echo ""

    if [ -z "$input_val" ]; then
        secret_value=$(get_random_string)
        local origin="Automatically generated"
    else
        secret_value="$input_val"
        local origin="User provided"
    fi

    # Writing to file
    echo "$secret_value" > "$file_path"
    if [ $? -ne 0 ]; then
        echo -e "${MSG_ERR} Unable to write to $file_path"
        exit 1
    fi

    # Leggendo online:
    # With docker compose secrets permissions on the host are the same as in the 
    # container. You can change the ownership of the file on the host to match the 
    # uid/gid of the user in the container, but otherwise I don't think there's
    # much that can  be done unfortunately.
    # Non ho ancora ben capito come gestire questa situazione, quindi per ora imposto
    # u+rw, g+r, o+r.
    chmod 644 "$file_path"
    if [ $? -ne 0 ]; then
        echo -e "${MSG_ERR} Unable to set 600 permissions on $file_path"
        exit 1
    fi

    echo -e "${MSG_OK} $secret_name saved to $file_path ($origin)"
}

# ------------------------

SECRETS_DIR="./secrets"
ENV_FILE="./.env"

# Secrets
if [ ! -d "$SECRETS_DIR" ]; then
    mkdir -p "$SECRETS_DIR"
    if [ $? -eq 0 ]; then
        echo -e "${MSG_OK} Directory created: $SECRETS_DIR"
    else
        echo -e "${MSG_ERR} Error creating directory $SECRETS_DIR"
        exit 1
    fi
fi

handle_secret "$SECRETS_DIR/db_root_password.txt" "MySQL Root Password"
handle_secret "$SECRETS_DIR/db_user_password.txt" "MySQL User Password"

# .env file
DEFAULT_DB_NAME="unibostu"
DEFAULT_DB_USER="unibostu"
if [ -s "$ENV_FILE" ]; then
    echo -e "${MSG_WARN} The .env file already exists. Skipping."
else
    echo -ne "${MSG_QUEST} Database Name [default: ${DEFAULT_DB_NAME}]: "
    read input_db
    DB_NAME=${input_db:-$DEFAULT_DB_NAME}

    echo -ne "${MSG_QUEST} Database User [default: ${DEFAULT_DB_USER}]: "
    read input_user
    DB_USER=${input_user:-$DEFAULT_DB_USER}

    cat > "$ENV_FILE" <<EOF
# MySQL Configuration
MYSQL_DATABASE=${DB_NAME}
MYSQL_USER=${DB_USER}
EOF

    if [ $? -eq 0 ]; then
        echo -e "${MSG_OK} .env file created successfully."
        echo -e "         Database: ${GREEN}${DB_NAME}${NC}"
        echo -e "         User:     ${GREEN}${DB_USER}${NC}"
    else
        echo -e "${MSG_ERR} Error writing to .env file"
        exit 1
    fi
fi

# --- SUMMARY ---
echo -e "\n-------------------------------------------------------"
if [ "$HAS_OPENSSL" = false ]; then
    echo -e "${MSG_WARN} SETUP COMPLETED WITH WARNING (No OpenSSL)"
else
    echo -e "${MSG_OK} SETUP COMPLETED SUCCESSFULLY"
fi
echo -e "-------------------------------------------------------"
echo -e "You can start the project with: ${GREEN}docker compose up -d --build${NC}"