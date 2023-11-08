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

echo -e "Create chain cert \n"
cat cert.pem > chain.pem
echo  -e "\n" >> chain.pem
cat intermediate.pem >> chain.pem
openssl crl2pkcs7 -nocrl -certfile chain.pem | openssl pkcs7 -print_certs -noout

echo -e "Create full chain cert \n"
cat chain.pem > fullchain.pem
echo  -e "\n" >> fullchain.pem
cat ca.pem >> fullchain.pem
openssl crl2pkcs7 -nocrl -certfile fullchain.pem | openssl pkcs7 -print_certs -noout


