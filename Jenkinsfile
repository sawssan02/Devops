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
                sh 'docker build -t ${DOCKER_IMAGE}:${IMAGE_TAG} -f simple_api/Dockerfile simple_api/'
            }
        }
        stage('Test Docker Image') {
            steps {
                sh 'docker run --rm -d -p 8080:8080 --name test_container ${DOCKER_IMAGE}:${IMAGE_TAG}'
                sh 'sleep 10'
                sh 'curl -f http://localhost:8080 || exit 1'
                sh 'docker stop test_container'
            }
        }
        stage('Push to Local Registry') {
            steps {
                sh 'docker push ${DOCKER_IMAGE}:${IMAGE_TAG}'
            }
        }
        stage('Deploy on AWS') {
            steps {
                sshagent(['AWS_SSH_CREDENTIALS']) {
                    sh '''
                    ssh -o StrictHostKeyChecking=no ${AWS_INSTANCE} "
                    docker pull ${DOCKER_IMAGE}:${IMAGE_TAG} && \
                    docker stop web_app || true && \
                    docker rm web_app || true && \
                    docker run -d -p 80:8080 --name web_app ${DOCKER_IMAGE}:${IMAGE_TAG}"
                    '''
                }
            }
        }
    }
}
