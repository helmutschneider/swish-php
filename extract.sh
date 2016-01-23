#!/bin/bash
# Script to extract certs & private keys from a PKCS12 bundle.

# Make sure we are using the script with 3 arguments.
if [ "$#" -ne 3 ]; then
    echo "Usage: ./extract.sh [IN_FILE] [IN_PASSWORD] [OUT_PASSWORD]"
    echo "Example: ./extract.sh bundle.p12 swish swish"
    exit 1
fi

openssl pkcs12 -in "$1" -cacerts -nokeys -out ca.crt -passin "pass:$2"
openssl pkcs12 -in "$1" -clcerts -nokeys -out cl.crt -passin "pass:$2"
openssl pkcs12 -in "$1" -nocerts -out key.pem -passin "pass:$2" -passout "pass:$3"
