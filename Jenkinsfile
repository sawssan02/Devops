pipeline {
    agent any
    environment {
        DOCKER_IMAGE = "localhost:5000/api:1.0"  // Utilisation de ton registry local
        AWS_INSTANCE = "ec2-user@13.61.3.10"
    }
    stages {
        stage('Build') {
            steps {
                // Construire l'image Docker
                sh 'docker build -t ${DOCKER_IMAGE}:${BUILD_NUMBER} .'
            }
        }
        stage('Test Docker Image') {
            steps {
                // Tester l'image construite en local
                sh 'docker run --rm -d -p 8080:8080 --name mytest ${DOCKER_IMAGE}:${BUILD_NUMBER}'
                sh 'sleep 10'  // Attendre le démarrage du conteneur
                sh 'curl -f http://localhost:8080 || exit 1'
                sh 'docker stop mytest'
            }
        }
        stage('Push to Local Registry') {
            steps {
                // Pousser l'image vers le registre local (localhost:5000)
                sh 'docker push ${DOCKER_IMAGE}:${BUILD_NUMBER}'
            }
        }
        stage('Deploy on AWS') {
            steps {
                // Déployer l'image sur AWS en utilisant SSH
                sshagent(['AWS_SSH_CREDENTIALS']) {
                    sh '''
                    ssh -o StrictHostKeyChecking=no ${AWS_INSTANCE} "docker pull ${DOCKER_IMAGE}:${BUILD_NUMBER} && \
                    docker stop web_app || true && \
                    docker rm web_app || true && \
                    docker run -d -p 80:8080 --name web_app ${DOCKER_IMAGE}:${BUILD_NUMBER}"
                    '''
                }
            }
        }
    }
}
