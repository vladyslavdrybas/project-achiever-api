#!/bin/bash

#If you are using intermediate certificate(s),
#you will need to make sure that the application using the certificate
#is sending the complete chain (server certificate and intermediate certificate).
#This depends on the application you are using that uses the certificate (always check the documentation),
#but usually you have to create a file containing the server certificate file and the intermediate certificate file.
#It is required to put the server certificate file first, and then the intermediate certificate file(s).
#When using the files in our example, we can create the correct file for the chain using the following command:

#check generated chain.pem
#expect something like this:
#subject=/CN=achievernotifier.com
#issuer=/C=PL/O=Unizeto Technologies S.A./OU=Certum Certification Authority/CN=Certum Domain Validation CA SHA2
#
#subject=/C=PL/O=Unizeto Technologies S.A./OU=Certum Certification Authority/CN=Certum Domain Validation CA SHA2
#issuer=/C=PL/O=Unizeto Technologies S.A./OU=Certum Certification Authority/CN=Certum Trusted Network CA

echo -e "\nVerify certificates are valid \n Values must match \n"
openssl x509 -noout -modulus -in cert.pem | openssl md5
openssl rsa -noout -modulus -in privatekey.pem | openssl md5

echo -e "\nVerify that the Public Keys contained in the Private Key file and the Main/Server Certificate are the same \n"
openssl x509 -in cert.pem -noout -pubkey
openssl rsa -in privatekey.pem -pubout

echo -e "\nVerify dates \n"
openssl x509 -noout -in cert.pem -dates
openssl x509 -noout -in intermediate.pem -dates
openssl x509 -noout -in ca.pem -dates

echo -e "\nVerify chain of certs \n"
openssl verify -CAfile ca.pem -untrusted intermediate.pem cert.pem

echo -e "\nCreate chain cert \n"
cat cert.pem > chain.pem
echo  -e "\n" >> chain.pem
cat intermediate.pem >> chain.pem
openssl crl2pkcs7 -nocrl -certfile chain.pem | openssl pkcs7 -print_certs -noout

echo -e "\nCreate full chain cert \n"
cat chain.pem > fullchain.pem
echo  -e "\n" >> fullchain.pem
cat ca.pem >> fullchain.pem
openssl crl2pkcs7 -nocrl -certfile fullchain.pem | openssl pkcs7 -print_certs -noout

echo -e "\nCreate key full chain cert \n"
cat privatekey.pem > keyfullchain.pem
echo  -e "\n" >> keyfullchain.pem
cat fullchain.pem >> keyfullchain.pem
openssl crl2pkcs7 -nocrl -certfile fullchain.pem | openssl pkcs7 -print_certs -noout
