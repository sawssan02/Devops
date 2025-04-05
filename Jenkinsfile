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
                dir('api') {
                    sh 'docker build -t $IMAGE_NAME .'
                }
            }
        }

        stage('Test Docker image') {
            steps {
                sh 'docker run -d -p 5000:5000 --name api_test -v $(pwd)/api/student_age.json:/data/student_age.json $IMAGE_NAME'
                sh 'sleep 5'
                sh 'curl -u admin:admin http://localhost:5000/SUPMIT/api/v1.0/get_student_ages'
                sh 'docker stop api_test && docker rm api_test'
            }
        }

        stage('Pousser sur Docker Hub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    sh 'echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin'
                    sh 'docker tag $IMAGE_NAME $REGISTRY/$IMAGE_NAME'
                    sh 'docker push $REGISTRY/$IMAGE_NAME'
                }
            }
        }

        stage('Déployer sur AWS') {
            steps {
                sshagent(['AWS_SSH_CREDENTIAL']) {
                    sh '''
                    ssh ec2-user@13.61.3.10 '
                        docker pull $REGISTRY/$IMAGE_NAME &&
                        docker stop api || true &&
                        docker rm api || true &&
                        docker run -d -p 5000:5000 --name api -v /home/ubuntu/data/student_age.json:/data/student_age.json $REGISTRY/$IMAGE_NAME
                    '
                    '''
                }
            }
        }
    }
}
