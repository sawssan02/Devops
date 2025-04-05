pipeline {
    agent any

    environment {
        IMAGE_NAME = "sawssan02/api:1.0"
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
                    sh 'docker build -t $IMAGE_NAME .'
                }
            }
        }

        stage('Test Docker image') {
            steps {
                // Supprimer le conteneur s'il existe déjà
                sh 'docker rm -f api_test || true'

                // Lancer le nouveau conteneur
                sh 'docker run -d -p 5000:5000 --name api_test -v /var/lib/jenkins/workspace/Deploye/simple_api:/data $IMAGE_NAME'
                sh 'sleep 5'
                sh 'curl -u admin:admin http://localhost:5000/SUPMIT/api/v1.0/get_student_ages'
                sh 'docker stop api_test && docker rm api_test'
            }
        }

        stage('Pousser sur Docker Hub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASSWORD')]) {
                    sh 'echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USER" --password-stdin'
                    sh 'docker push $IMAGE_NAME'
                }
            }
        }

       stage('Déployer sur AWS') {
    steps {
        withCredentials([string(credentialsId: 'DOCKER_PASSWORD_CREDENTIAL', variable: 'DOCKER_PASSWORD')]) {
            // Définir l'utilisateur Docker
            def DOCKER_USER = 'sawssan02'

            sshagent(['AWS_SSH_CREDENTIAL']) {
                sh """
                    # Connexion SSH à l'instance EC2 et exécution des commandes Docker
                    ssh ec2-user@13.61.3.10 -o StrictHostKeyChecking=no <<EOF
                        # Connexion Docker avec le mot de passe
                        echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USER" --password-stdin

                        # Tirer l'image Docker et la déployer
                        docker pull $REGISTRY/$IMAGE_NAME &&

                        # Arrêter et supprimer l'ancien conteneur (s'il existe)
                        docker stop api || true &&
                        docker rm api || true &&

                        # Lancer le nouveau conteneur
                        docker run -d -p 5000:5000 --name api -v /home/ubuntu/data/student_age.json:/data/student_age.json $REGISTRY/$IMAGE_NAME
                    EOF
                """
            }
        }
    }
}

    }
}
