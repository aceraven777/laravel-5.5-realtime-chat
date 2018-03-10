node {
    stage('preparation') {
    }
    stage("composer_install") {
        // Run `composer update` as a shell script
        sh 'composer install'
    }
    stage("phpunit") {
        // Run PHPUnit
        sh 'pwd'
        sh 'ls'
    }
}