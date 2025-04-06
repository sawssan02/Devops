pipeline {
    agent any

    environment {
        DOCKER_IMAGE_FRONTEND = 'sawssan02/frontend:1.0'
        DOCKER_IMAGE_BACKEND = 'sawssan02/backend:1.0'
        DOCKER_REGISTRY = 'docker.io'
        AWS_EC2_INSTANCE = 'ec2-user@13.61.3.10'
        EC2_PRIVATE_KEY = credentials('AWS_SSH_CREDENTIAL') 
    }

    stages {
        stage('Cloner le Dépôt') {
            steps {
                git branch: 'main', url: 'https://github.com/sawssan02/Devops.git'
            }
        }

        stage('Construire l\'Image Docker Frontend') {
            steps {
                script {
                    sh 'docker build -t $DOCKER_IMAGE_FRONTEND ./website'
                }
            }
        }

        stage('Construire l\'Image Docker Backend') {
            steps {
                script {
                    sh 'docker build -t $DOCKER_IMAGE_BACKEND ./simple_api'
                }
            }
        }

        stage('Pousser les Images sur Docker Hub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASSWORD')]) {
                    script {
                        sh 'echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USER" --password-stdin'
                        sh 'docker push $DOCKER_IMAGE_FRONTEND'
                        sh 'docker push $DOCKER_IMAGE_BACKEND'
                    }
                }
            }
        }

        stage('Déployer sur AWS EC2') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKER_USERNAME', passwordVariable: 'DOCKER_PASSWORD')]) {
                    script {
                        sh """
                            # Copier le fichier JSON de l'étudiant sur le serveur EC2
                            scp -i $EC2_PRIVATE_KEY -o StrictHostKeyChecking=no simple_api/student_age.json $AWS_EC2_INSTANCE:/home/ec2-user/student_age.json

                            # Connecter à l'instance EC2 et effectuer le déploiement
                            ssh -i $EC2_PRIVATE_KEY -o StrictHostKeyChecking=no $AWS_EC2_INSTANCE '
                                # Se connecter à Docker Hub et récupérer les dernières images
                                echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USERNAME" --password-stdin &&
                                docker pull $DOCKER_REGISTRY/$DOCKER_IMAGE_FRONTEND &&
                                docker pull $DOCKER_REGISTRY/$DOCKER_IMAGE_BACKEND &&

                                # Arrêter et supprimer les conteneurs existants
                                docker stop frontend || true && docker rm frontend || true &&
                                docker stop backend || true && docker rm backend || true &&

                                # Démarrer les nouveaux conteneurs
                                docker run -d -p 5000:5000 --name backend -v /home/ec2-user/data:/data $DOCKER_REGISTRY/$DOCKER_IMAGE_BACKEND &&
                                docker run -d -p 80:80 --name frontend $DOCKER_REGISTRY/$DOCKER_IMAGE_FRONTEND
                                docker cp /home/ec2-user/student_age.json backend:/data
                            '
                        """
                    }
                }
            }
        }
    }

    post {
        success {
            echo 'Déploiement terminé avec succès.'
        }
        failure {
            echo 'Échec du déploiement.'
        }
    }
}
