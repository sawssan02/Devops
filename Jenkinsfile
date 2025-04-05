pipeline {
    agent any

    environment {
        IMAGE_NAME = "api:1.0"
        REGISTRY = "docker.io/sawssan02"
    }

    stages {
        stage('Cloner le repo') {
            steps {
                git branch: 'main', url: 'https://github.com/sawssan02/Devops.git'
            }
        }

        stage('Build Docker image') {
            steps {
                dir('simple_api') {
                    sh 'docker build --no-cache -t $IMAGE_NAME .'
                    sh 'docker tag $IMAGE_NAME $REGISTRY/$IMAGE_NAME'
                }
            }
        }
        stage('Construire l\'image Nginx') {
            steps {
                dir('website') {
                    // Construire l'image Nginx
                    // Vérifier si le Dockerfile est présent
                    sh 'ls -al'
                    sh 'docker build -t nginx-server .'
                    sh 'docker tag nginx-server $REGISTRY/nginx-server'
                }
            }
        }


        stage('Test Docker image') {
            steps {
                // Supprimer le conteneur s'il existe déjà
                sh 'docker rm -f api_test || true'
                sh 'docker rm -f test-nginx || true'
                // Lancer le nouveau conteneur
                sh 'docker run -d -p 5000:5000 --name api_test -v /var/lib/jenkins/workspace/Deploye/simple_api:/data $IMAGE_NAME'
                sh 'docker run -d -p 80:80 --name test-nginx nginx-server'
                sh 'docker cp simple_api/student_age.json api_test:/data'
                sh 'sleep 5'
                sh 'curl -u root:root -X GET http://localhost:5000/supmit/api/v1.0/get_student_ages'
                sh 'docker stop api_test && docker rm api_test'
                sh 'docker stop test-nginx && docker rm test-nginx'
            }
        }

        stage('Pousser sur Docker Hub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASSWORD')]) {
                    sh 'echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USER" --password-stdin'
                    sh 'docker push $REGISTRY/$IMAGE_NAME'
                    sh 'docker push $REGISTRY/nginx-server'
                }
            }
        }

       stage('Déployer sur AWS') {
    steps {
        withCredentials([string(credentialsId: 'DOCKER_PASSWORD_CREDENTIAL', variable: 'DOCKER_PASSWORD')]) {
            sshagent(['AWS_SSH_CREDENTIAL']) {
                sh """
                    ssh ec2-user@13.61.3.10 -o StrictHostKeyChecking=no 'echo "$DOCKER_PASSWORD" | docker login -u sawssan02 --password-stdin && \
                    docker pull $REGISTRY/$IMAGE_NAME && \
                    docker pull $REGISTRY/nginx-server && \
                    docker stop api || true && \
                    docker rm api || true && \
                    docker run -d -p 5000:5000 --name api -v /home/ubuntu/data:/data $REGISTRY/$IMAGE_NAME && \
                    docker run -d -p 80:80 --name nginx $REGISTRY/nginx-server && \
                    docker cp simple_api/student_age.json api:/data'
                """
            }
        }
    }
}
// Nouvelle étape pour exécuter docker-compose
        
    }
}
