# Dockerfile Documentation:
#
# 1. Base Image:
#    - Uses the python:3.8-buster image to ensure a consistent Python 3.8 environment on Debian Buster.
#
# 2. Maintainer Information:
#    - Includes metadata (name and email) about the Dockerfile maintainer.
#
# 3. Application Script:
#    - Copies the 'student_age.py' script to the container’s root directory.
#
# 4. System Dependencies:
#    - Updates the package list and installs Python development headers along with SASL, LDAP, and SSL libraries
#      which are prerequisites for certain Python packages.
#
# 5. Python Dependencies:
#    - Copies the 'requirements.txt' file into the container.
#    - Installs all the Python packages listed in 'requirements.txt' using pip.
#
# 6. Data Directory and Volume:
#    - Creates a directory '/data' intended for persistent data storage.
#    - Declares '/data' as a volume, allowing the data to survive container re-creation.
#
# 7. Network Configuration:
#    - Exposes port 5000 to provide access to the running application (likely a web service).
#
# 8. Startup Command:
#    - Specifies that the container should run the 'student_age.py' script using Python3 as its main process.

FROM python:3.8-buster 

LABEL maintainer="name: Mohammed Majidi, email: mohammedmajidi321@gmail.com"

COPY student_age.py /

RUN apt update -y && apt install python3-dev libsasl2-dev libldap2-dev libssl-dev -y 
RUN apt-get update && apt-get install -y libcrypt-dev

COPY requirements.txt /

RUN pip3 install -r requirements.txt

RUN mkdir -p /data
COPY student_age.json /data/


VOLUME [ "/data" ]

EXPOSE 5000

CMD [ "python3", "student_age.py" ]
