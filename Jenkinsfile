pipeline {
    agent any
    environment {
        DOCKER_IMAGE = "localhost:5000/api"
        IMAGE_TAG = "1.0-${BUILD_NUMBER}"  // Utilisation du "-" au lieu de ":"
        AWS_INSTANCE = "ec2-user@13.61.3.10"
    }
    stages {
        stage('Build') {
            steps {
                // Construction de l'image Docker avec le bon tag
                sh 'docker build -t ${DOCKER_IMAGE}-${IMAGE_TAG} -f simple_api/Dockerfile simple_api/'
            }
        }
        stage('Test Docker Image') {
            steps {
                // Lancer le conteneur avec le nouveau tag
                sh 'docker run --name mytest -d -p 5001:5000 ${DOCKER_IMAGE}-${IMAGE_TAG}'
                
                // Tester le conteneur
                sh 'docker logs mytest'

                // Arrêter et nettoyer le conteneur après le test
                sh 'docker stop mytest'
                sh 'docker rm mytest'
            }
        }
        stage('Push to Local Registry') {
            steps {
                // Pousser l'image vers le registre local avec le tag modifié
                sh 'docker push ${DOCKER_IMAGE}-${IMAGE_TAG}'
            }
        }
        stage('Deploy on AWS') {
            steps {
                sshagent(['JenkinsDocker']) {
                    sh '''
                    ssh -o StrictHostKeyChecking=no ${AWS_INSTANCE} "
                    docker pull ${DOCKER_IMAGE}-${IMAGE_TAG} && \
                    docker stop web_app || true && \
                    docker rm web_app || true && \
                    docker run -d -p 80:8080 --name web_app ${DOCKER_IMAGE}-${IMAGE_TAG}"
                    '''
                }
            }
        }
    }
}
