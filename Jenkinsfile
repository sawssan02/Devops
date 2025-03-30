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
                script {
                    try {
                        // Construire l'image Docker
                        sh 'docker build -t ${DOCKER_IMAGE}:${BUILD_NUMBER} .'
                    } catch (Exception e) {
                        // Afficher l'erreur dans la console
                        currentBuild.result = 'FAILURE'
                        echo "Erreur dans la construction de l'image Docker: ${e.message}"
                        throw e
                    }
                }
            }
        }
        
        // Autres étapes commentées pour ce test
        // stage('Test Docker Image') {
        //    steps {
        //        sh 'docker run --rm -d -p 8080:8080 --name mytest ${DOCKER_IMAGE}:${BUILD_NUMBER}'
        //        sh 'sleep 10'
        //        sh 'curl -f http://localhost:8080 || exit 1'
        //        sh 'docker stop mytest'
        //    }
        // }
        // stage('Push to Local Registry') {
        //    steps {
        //        sh 'docker push ${DOCKER_IMAGE}:${BUILD_NUMBER}'
        //    }
        // }
        // stage('Deploy on AWS') {
        //    steps {
        //        sshagent(['AWS_SSH_CREDENTIALS']) {
        //            sh '''
        //            ssh -i ${SSH_KEY_PATH} -o StrictHostKeyChecking=no ${AWS_INSTANCE} "docker pull ${DOCKER_IMAGE}:${BUILD_NUMBER} && \
        //            docker stop web_app || true && \
        //            docker rm web_app || true && \
        //            docker run -d -p 80:8080 --name web_app ${DOCKER_IMAGE}:${BUILD_NUMBER}"
        //            '''
        //        }
        //    }
        // }
    }
}
