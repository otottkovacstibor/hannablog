node('master') {
  def git
  try {
    properties([buildDiscarder(logRotator(numToKeepStr: '5')), [$class: 'GithubProjectProperty', projectUrlStr: 'https://github.com/Tibi02/hannablog'], pipelineTriggers([githubPush()])])
    
    ws('/disk/docker/hannablog') {
      stage('Git checkout') {
        ansiColor('xterm') {
          git = checkout([$class: 'GitSCM', branches: [[name: '*/master']], browser: [$class: 'GithubWeb', repoUrl: 'https://github.com/Tibi02/hannablog'], doGenerateSubmoduleConfigurations: false, extensions: [[$class: 'MessageExclusion', excludedMessage: 'skip ci'], [$class: 'LocalBranch', localBranch: 'master']], userRemoteConfigs: [[credentialsId: 'oktibor-ci', url: 'git@github.com:Tibi02/hannablog.git']]])
          setGithubStatus('PENDING', git.GIT_COMMIT)
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

    setGithubStatus('SUCCESS', git.GIT_COMMIT)
  } catch(e) {
    setGithubStatus('FAILURE', git.GIT_COMMIT)
  }
}

def setGithubStatus(status, commitId) {
  githubNotify(account: 'Tibi02', repo: 'hannablog', context: 'Deploy', sha: commitId, credentialsId: 'oktibor-ci-github', status: status)
}
