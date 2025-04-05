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

        stage('Build Docker images') {
            steps {
                // Construire l'image API
                dir('simple_api') {
                    sh 'docker build --no-cache -t $IMAGE_NAME .'
                    sh 'docker tag $IMAGE_NAME $REGISTRY/$IMAGE_NAME'
                }
                // Construire l'image Nginx avec PHP
                dir('website') {
                    sh 'docker build -t nginx-php .'
                    sh 'docker tag nginx-php $REGISTRY/nginx-php'
                }
            }
        }

        stage('Test Docker images') {
            steps {
                // Tester les images Docker en local
                sh 'docker-compose -f docker-compose.yml up -d'
                sh 'sleep 10'
                sh 'curl -I http://localhost:80' // Vérifier que Nginx fonctionne
                sh 'curl -I http://localhost:5000/supmit/api/v1.0/get_student_ages' // Vérifier l'API
            }
        }
        stage('Push Docker images to Docker Hub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASSWORD')]) {
                    sh 'echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USER" --password-stdin'
                    sh 'docker push $REGISTRY/$IMAGE_NAME'
                    sh 'docker push $REGISTRY/nginx-php'
                }
            }
        }
      stage('Deploy to AWS') {
            steps {
                withCredentials([string(credentialsId: 'DOCKER_PASSWORD_CREDENTIAL', variable: 'DOCKER_PASSWORD')]) {
                    sshagent(['AWS_SSH_CREDENTIAL']) {
                        sh '''
                            # Copier les fichiers sur l'EC2
                            scp -o StrictHostKeyChecking=no simple_api/student_age.json ec2-user@13.61.3.10:/home/ec2-user/student_age.json

                            # Se connecter à l'instance EC2 et déployer avec Docker Compose
                            ssh ec2-user@13.61.3.10 -o StrictHostKeyChecking=no '
                                echo "$DOCKER_PASSWORD" | docker login -u sawssan02 --password-stdin && \
                                docker pull docker.io/sawssan02/api:1.0 && \
                                docker pull docker.io/sawssan02/nginx-php && \
                                
                                # Supprimer les conteneurs existants si nécessaire
                                docker-compose -f /home/ec2-user/docker-compose.yml down && \
                                
                                # Démarrer les nouveaux conteneurs
                                docker-compose -f /home/ec2-user/docker-compose.yml up -d && \
                                
                                # Copier les fichiers dans le conteneur API
                                docker cp /home/ec2-user/student_age.json api:/data
                            '
                        '''
                    }
                }
            }
        }
// Nouvelle étape pour exécuter docker-compose
        
    }
}
