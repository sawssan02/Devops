pipeline {
    agent any

    environment {
        // Définition des variables d'environnement
        ImageRegistry = 'api:1.0'      // Nom de l'image Docker
        EC2_IP = '13.60.14.131'        // IP de la machine EC2 AWS
        DockerComposeFile = 'docker-compose.yml'  // Fichier Docker Compose
        DotEnvFile = 'requirements.txt'  // Fichier des dépendances
    }

    stages {
        
        // Étape de construction de l'image Docker
        stage('Build Docker Image') {
            steps {
                script {
                    echo "Building Docker Image..."
                    sh "docker build -t ${ImageRegistry}/${JOB_NAME}:${BUILD_NUMBER} ."
                }
            }
        }

        // Étape de test de l'image Docker (exécution locale et vérification)
        stage('Test Docker Image') {
            steps {
                script {
                    echo "Testing Docker Image..."
                    sh "docker run -d -p 8080:80 ${ImageRegistry}/${JOB_NAME}:${BUILD_NUMBER}"
                    sh "curl -f http://localhost:8080 || exit 1"  // Vérification de l'accessibilité
                }
            }
        }

        // Étape de push de l'image Docker vers Docker Hub
        stage('Push Docker Image to Docker Hub') {
            steps {
                script {
                    echo "Pushing Docker Image to Docker Hub..."
                    withCredentials([usernamePassword(credentialsId: 'docker-login', passwordVariable: 'PASS', usernameVariable: 'USER')]) {
                        sh "echo $PASS | docker login -u $USER --password-stdin"
                        sh "docker push ${ImageRegistry}/${JOB_NAME}:${BUILD_NUMBER}"
                    }
                }
            }
        }

        // Étape de déploiement avec Docker Compose sur la machine EC2
        stage('Deploy to EC2 via Docker Compose') {
            steps {
                script {
                    echo "Deploying Docker Compose on EC2 instance..."
                    sshagent(['ec2']) {
                        // Transfert des fichiers Docker Compose et requirements.txt sur la machine EC2
                        sh """
                        scp -o StrictHostKeyChecking=no ${DockerComposeFile} ${DotEnvFile} ubuntu@${EC2_IP}:/home/ubuntu/
                        ssh -o StrictHostKeyChecking=no ubuntu@${EC2_IP} 'docker-compose -f /home/ubuntu/${DockerComposeFile} --env-file /home/ubuntu/${DotEnvFile} down'
                        ssh -o StrictHostKeyChecking=no ubuntu@${EC2_IP} 'docker-compose -f /home/ubuntu/${DockerComposeFile} --env-file /home/ubuntu/${DotEnvFile} up -d'
                        """
                    }
                }
            }
        }

        // Étape de nettoyage (facultative mais recommandée pour libérer les ressources)
        stage('Clean Up') {
            steps {
                script {
                    echo "Cleaning up..."
                    sh "docker system prune -f"  // Suppression des images et containers inutiles
                }
            }
        }
    }

    post {
        always {
            echo "Pipeline completed."
        }
        success {
            echo "Pipeline succeeded."
        }
        failure {
            echo "Pipeline failed."
        }
    }
}
