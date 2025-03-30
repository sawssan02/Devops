pipeline {
    agent any
    environment {
        DOCKER_IMAGE = "localhost:5000/api:1.0"
        AWS_INSTANCE = "ec2-user@13.61.3.10"
        SSH_KEY_PATH = "C:\\Users\\sawssan\\Downloads\\JenkinsDocker.pem"
    }
    stages {
        stage('Build') {
            steps {
                // Construire l'image Docker
                sh 'docker build -t ${DOCKER_IMAGE}:${BUILD_NUMBER} .'
            }
        }
        // Autres étapes commentées pour ce test
        // stage('Test Docker Image') { ... }
        // stage('Push to Local Registry') { ... }
        // stage('Deploy on AWS') { ... }
    }
}
