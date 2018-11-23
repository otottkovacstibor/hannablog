node('master') {
  properties([buildDiscarder(logRotator(numToKeepStr: '5')), [$class: 'GithubProjectProperty', projectUrlStr: 'https://github.com/Tibi02/hannablog'], pipelineTriggers([githubPush()])])
  
  ws('/disk/docker/hannablog') {
    stage('Git checkout') {
      ansiColor('xterm') {
        checkout([$class: 'GitSCM', branches: [[name: '*/master']], doGenerateSubmoduleConfigurations: false, extensions: [[$class: 'LocalBranch', localBranch: 'master']], submoduleCfg: [], userRemoteConfigs: [[credentialsId: 'oktibor-ci', url: 'git@github.com:Tibi02/hannablog.git']]])
        sh('chown -R www-data:www-data .')
      }
    }

    stage('Docker pull') {
      ansiColor('xterm') {
        sh('docker-compose pull')
      }
    }

    stage('Recreate container') {
      ansiColor('xterm') {
        withCredentials([usernamePassword(credentialsId: 'mysql', passwordVariable: 'MYSQL_PASSWORD', usernameVariable: 'MYSQL_USER')]) {
          sh('docker-compose -f docker-compose.yml up -d --force-recreate')
        }
      }
    }
  }
}
