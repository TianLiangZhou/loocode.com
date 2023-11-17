<?php

namespace App;

use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Customize\AbstractControl;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\OctopusPressKernel;
use OctopusPress\Bundle\Plugin\PluginInterface;
use OctopusPress\Bundle\Plugin\PluginProviderInterface;

class Kernel extends OctopusPressKernel implements PluginInterface
{

    public function launcher(Bridger $bridger): void
    {

        $bridger->getMeta()->registerPost(Post::TYPE_POST, 'devicon', [], [
            'type' => AbstractControl::SELECT_SEARCH,
            'label' => '主标签',
            'options' => [
                [
                    "value" => "devicon-adonisjs-original",
                    "label" => "adonisjs"
                ],
                [
                    "value" => "devicon-aftereffects-plain",
                    "label" => "aftereffects"
                ],
                [
                    "value" => "devicon-amazonwebservices-original",
                    "label" => "amazonwebservices"
                ],
                [
                    "value" => "devicon-android-plain",
                    "label" => "android"
                ],
                [
                    "value" => "devicon-androidstudio-plain",
                    "label" => "androidstudio"
                ],
                [
                    "value" => "devicon-aarch64-plain",
                    "label" => "aarch64"
                ],
                [
                    "value" => "devicon-angularjs-plain",
                    "label" => "angularjs"
                ],
                [
                    "value" => "devicon-ansible-plain",
                    "label" => "ansible"
                ],
                [
                    "value" => "devicon-apache-plain",
                    "label" => "apache"
                ],
                [
                    "value" => "devicon-apachekafka-original",
                    "label" => "apachekafka"
                ],
                [
                    "value" => "devicon-appcelerator-original",
                    "label" => "appcelerator"
                ],
                [
                    "value" => "devicon-apple-original",
                    "label" => "apple"
                ],
                [
                    "value" => "devicon-appwrite-plain",
                    "label" => "appwrite"
                ],
                [
                    "value" => "devicon-arduino-plain",
                    "label" => "arduino"
                ],
                [
                    "value" => "devicon-atom-original",
                    "label" => "atom"
                ],
                [
                    "value" => "devicon-azure-plain",
                    "label" => "azure"
                ],
                [
                    "value" => "devicon-babel-plain",
                    "label" => "babel"
                ],
                [
                    "value" => "devicon-backbonejs-plain",
                    "label" => "backbonejs"
                ],
                [
                    "value" => "devicon-bamboo-original",
                    "label" => "bamboo"
                ],
                [
                    "value" => "devicon-bash-plain",
                    "label" => "bash"
                ],
                [
                    "value" => "devicon-behance-plain",
                    "label" => "behance"
                ],
                [
                    "value" => "devicon-bitbucket-original",
                    "label" => "bitbucket"
                ],
                [
                    "value" => "devicon-bootstrap-plain",
                    "label" => "bootstrap"
                ],
                [
                    "value" => "devicon-bulma-plain",
                    "label" => "bulma"
                ],
                [
                    "value" => "devicon-bower-plain",
                    "label" => "bower"
                ],
                [
                    "value" => "devicon-c-plain",
                    "label" => "c"
                ],
                [
                    "value" => "devicon-cakephp-plain",
                    "label" => "cakephp"
                ],
                [
                    "value" => "devicon-canva-original",
                    "label" => "canva"
                ],
                [
                    "value" => "devicon-centos-plain",
                    "label" => "centos"
                ],
                [
                    "value" => "devicon-ceylon-plain",
                    "label" => "ceylon"
                ],
                [
                    "value" => "devicon-chrome-plain",
                    "label" => "chrome"
                ],
                [
                    "value" => "devicon-circleci-plain",
                    "label" => "circleci"
                ],
                [
                    "value" => "devicon-clojure-line",
                    "label" => "clojure"
                ],
                [
                    "value" => "devicon-cmake-plain",
                    "label" => "cmake"
                ],
                [
                    "value" => "devicon-clojurescript-plain",
                    "label" => "clojurescript"
                ],
                [
                    "value" => "devicon-codecov-plain",
                    "label" => "codecov"
                ],
                [
                    "value" => "devicon-codeigniter-plain",
                    "label" => "codeigniter"
                ],
                [
                    "value" => "devicon-codepen-plain",
                    "label" => "codepen"
                ],
                [
                    "value" => "devicon-coffeescript-original",
                    "label" => "coffeescript"
                ],
                [
                    "value" => "devicon-composer-line",
                    "label" => "composer"
                ],
                [
                    "value" => "devicon-confluence-original",
                    "label" => "confluence"
                ],
                [
                    "value" => "devicon-couchdb-plain",
                    "label" => "couchdb"
                ],
                [
                    "value" => "devicon-cplusplus-plain",
                    "label" => "cplusplus"
                ],
                [
                    "value" => "devicon-csharp-plain",
                    "label" => "csharp"
                ],
                [
                    "value" => "devicon-css3-plain",
                    "label" => "css3"
                ],
                [
                    "value" => "devicon-cucumber-plain",
                    "label" => "cucumber"
                ],
                [
                    "value" => "devicon-crystal-original",
                    "label" => "crystal"
                ],
                [
                    "value" => "devicon-d3js-plain",
                    "label" => "d3js"
                ],
                [
                    "value" => "devicon-dart-plain",
                    "label" => "dart"
                ],
                [
                    "value" => "devicon-debian-plain",
                    "label" => "debian"
                ],
                [
                    "value" => "devicon-denojs-original",
                    "label" => "denojs"
                ],
                [
                    "value" => "devicon-devicon-plain",
                    "label" => "devicon"
                ],
                [
                    "value" => "devicon-django-plain",
                    "label" => "django"
                ],
                [
                    "value" => "devicon-docker-plain",
                    "label" => "docker"
                ],
                [
                    "value" => "devicon-doctrine-plain",
                    "label" => "doctrine"
                ],
                [
                    "value" => "devicon-dot-net-plain",
                    "label" => "dot"
                ],
                [
                    "value" => "devicon-dotnetcore-plain",
                    "label" => "dotnetcore"
                ],
                [
                    "value" => "devicon-drupal-plain",
                    "label" => "drupal"
                ],
                [
                    "value" => "devicon-digitalocean-plain",
                    "label" => "digitalocean"
                ],
                [
                    "value" => "devicon-discordjs-plain",
                    "label" => "discordjs"
                ],
                [
                    "value" => "devicon-electron-original",
                    "label" => "electron"
                ],
                [
                    "value" => "devicon-eleventy-plain",
                    "label" => "eleventy"
                ],
                [
                    "value" => "devicon-elixir-plain",
                    "label" => "elixir"
                ],
                [
                    "value" => "devicon-elm-plain",
                    "label" => "elm"
                ],
                [
                    "value" => "devicon-ember-original-wordmark",
                    "label" => "ember"
                ],
                [
                    "value" => "devicon-embeddedc-plain",
                    "label" => "embeddedc"
                ],
                [
                    "value" => "devicon-erlang-plain",
                    "label" => "erlang"
                ],
                [
                    "value" => "devicon-eslint-original",
                    "label" => "eslint"
                ],
                [
                    "value" => "devicon-express-original",
                    "label" => "express"
                ],
                [
                    "value" => "devicon-facebook-plain",
                    "label" => "facebook"
                ],
                [
                    "value" => "devicon-feathersjs-original",
                    "label" => "feathersjs"
                ],
                [
                    "value" => "devicon-figma-plain",
                    "label" => "figma"
                ],
                [
                    "value" => "devicon-filezilla-plain",
                    "label" => "filezilla"
                ],
                [
                    "value" => "devicon-firebase-plain",
                    "label" => "firebase"
                ],
                [
                    "value" => "devicon-firefox-plain",
                    "label" => "firefox"
                ],
                [
                    "value" => "devicon-flask-original",
                    "label" => "flask"
                ],
                [
                    "value" => "devicon-flutter-plain",
                    "label" => "flutter"
                ],
                [
                    "value" => "devicon-foundation-plain",
                    "label" => "foundation"
                ],
                [
                    "value" => "devicon-fsharp-plain",
                    "label" => "fsharp"
                ],
                [
                    "value" => "devicon-gatling-plain",
                    "label" => "gatling"
                ],
                [
                    "value" => "devicon-gatsby-plain",
                    "label" => "gatsby"
                ],
                [
                    "value" => "devicon-rect-plain",
                    "label" => "rect"
                ],
                [
                    "value" => "devicon-gcc-plain",
                    "label" => "gcc"
                ],
                [
                    "value" => "devicon-gentoo-plain-wordmark",
                    "label" => "gentoo"
                ],
                [
                    "value" => "devicon-gimp-plain",
                    "label" => "gimp"
                ],
                [
                    "value" => "devicon-git-plain",
                    "label" => "git"
                ],
                [
                    "value" => "devicon-github-original",
                    "label" => "github"
                ],
                [
                    "value" => "devicon-gitlab-plain",
                    "label" => "gitlab"
                ],
                [
                    "value" => "devicon-gitter-plain",
                    "label" => "gitter"
                ],
                [
                    "value" => "devicon-go-original-wordmark",
                    "label" => "go"
                ],
                [
                    "value" => "devicon-google-plain",
                    "label" => "google"
                ],
                [
                    "value" => "devicon-googlecloud-plain",
                    "label" => "googlecloud"
                ],
                [
                    "value" => "devicon-gradle-plain",
                    "label" => "gradle"
                ],
                [
                    "value" => "devicon-grafana-original",
                    "label" => "grafana"
                ],
                [
                    "value" => "devicon-grails-plain",
                    "label" => "grails"
                ],
                [
                    "value" => "devicon-graphql-plain",
                    "label" => "graphql"
                ],
                [
                    "value" => "devicon-groovy-plain",
                    "label" => "groovy"
                ],
                [
                    "value" => "devicon-grunt-plain",
                    "label" => "grunt"
                ],
                [
                    "value" => "devicon-gulp-plain",
                    "label" => "gulp"
                ],
                [
                    "value" => "devicon-godot-plain",
                    "label" => "godot"
                ],
                [
                    "value" => "devicon-haskell-plain",
                    "label" => "haskell"
                ],
                [
                    "value" => "devicon-handlebars-plain",
                    "label" => "handlebars"
                ],
                [
                    "value" => "devicon-haxe-plain",
                    "label" => "haxe"
                ],
                [
                    "value" => "devicon-heroku-original",
                    "label" => "heroku"
                ],
                [
                    "value" => "devicon-html5-plain",
                    "label" => "html5"
                ],
                [
                    "value" => "devicon-hugo-plain",
                    "label" => "hugo"
                ],
                [
                    "value" => "devicon-ie10-original",
                    "label" => "ie10"
                ],
                [
                    "value" => "devicon-ifttt-original",
                    "label" => "ifttt"
                ],
                [
                    "value" => "devicon-illustrator-plain",
                    "label" => "illustrator"
                ],
                [
                    "value" => "devicon-inkscape-plain",
                    "label" => "inkscape"
                ],
                [
                    "value" => "devicon-intellij-plain",
                    "label" => "intellij"
                ],
                [
                    "value" => "devicon-ionic-original",
                    "label" => "ionic"
                ],
                [
                    "value" => "devicon-jamstack-original",
                    "label" => "jamstack"
                ],
                [
                    "value" => "devicon-jasmine-plain",
                    "label" => "jasmine"
                ],
                [
                    "value" => "devicon-java-plain",
                    "label" => "java"
                ],
                [
                    "value" => "devicon-javascript-plain",
                    "label" => "javascript"
                ],
                [
                    "value" => "devicon-jeet-plain",
                    "label" => "jeet"
                ],
                [
                    "value" => "devicon-jest-plain",
                    "label" => "jest"
                ],
                [
                    "value" => "devicon-jenkins-line",
                    "label" => "jenkins"
                ],
                [
                    "value" => "devicon-jetbrains-plain",
                    "label" => "jetbrains"
                ],
                [
                    "value" => "devicon-jira-plain",
                    "label" => "jira"
                ],
                [
                    "value" => "devicon-jquery-plain",
                    "label" => "jquery"
                ],
                [
                    "value" => "devicon-julia-plain",
                    "label" => "julia"
                ],
                [
                    "value" => "devicon-jupyter-plain",
                    "label" => "jupyter"
                ],
                [
                    "value" => "devicon-kaggle-original",
                    "label" => "kaggle"
                ],
                [
                    "value" => "devicon-karma-plain",
                    "label" => "karma"
                ],
                [
                    "value" => "devicon-kotlin-plain",
                    "label" => "kotlin"
                ],
                [
                    "value" => "devicon-knockout-plain-wordmark",
                    "label" => "knockout"
                ],
                [
                    "value" => "devicon-krakenjs-plain",
                    "label" => "krakenjs"
                ],
                [
                    "value" => "devicon-kubernetes-plain",
                    "label" => "kubernetes"
                ],
                [
                    "value" => "devicon-labview-plain",
                    "label" => "labview"
                ],
                [
                    "value" => "devicon-laravel-plain",
                    "label" => "laravel"
                ],
                [
                    "value" => "devicon-latex-original",
                    "label" => "latex"
                ],
                [
                    "value" => "devicon-less-plain-wordmark",
                    "label" => "less"
                ],
                [
                    "value" => "devicon-linkedin-plain",
                    "label" => "linkedin"
                ],
                [
                    "value" => "devicon-lua-plain",
                    "label" => "lua"
                ],
                [
                    "value" => "devicon-linux-plain",
                    "label" => "linux"
                ],
                [
                    "value" => "devicon-materialui-plain",
                    "label" => "materialui"
                ],
                [
                    "value" => "devicon-matlab-plain",
                    "label" => "matlab"
                ],
                [
                    "value" => "devicon-magento-original",
                    "label" => "magento"
                ],
                [
                    "value" => "devicon-markdown-original",
                    "label" => "markdown"
                ],
                [
                    "value" => "devicon-maya-plain",
                    "label" => "maya"
                ],
                [
                    "value" => "devicon-meteor-plain",
                    "label" => "meteor"
                ],
                [
                    "value" => "devicon-minitab-plain",
                    "label" => "minitab"
                ],
                [
                    "value" => "devicon-mocha-plain",
                    "label" => "mocha"
                ],
                [
                    "value" => "devicon-modx-plain",
                    "label" => "modx"
                ],
                [
                    "value" => "devicon-mongodb-plain",
                    "label" => "mongodb"
                ],
                [
                    "value" => "devicon-moodle-plain",
                    "label" => "moodle"
                ],
                [
                    "value" => "devicon-msdos-line",
                    "label" => "msdos"
                ],
                [
                    "value" => "devicon-mysql-plain",
                    "label" => "mysql"
                ],
                [
                    "value" => "devicon-neo4j-plain",
                    "label" => "neo4j"
                ],
                [
                    "value" => "devicon-nestjs-plain",
                    "label" => "nestjs"
                ],
                [
                    "value" => "devicon-networkx-original",
                    "label" => "networkx"
                ],
                [
                    "value" => "devicon-nextjs-original",
                    "label" => "nextjs"
                ],
                [
                    "value" => "devicon-nginx-original",
                    "label" => "nginx"
                ],
                [
                    "value" => "devicon-nixos-plain",
                    "label" => "nixos"
                ],
                [
                    "value" => "devicon-nodejs-plain",
                    "label" => "nodejs"
                ],
                [
                    "value" => "devicon-nodewebkit-plain",
                    "label" => "nodewebkit"
                ],
                [
                    "value" => "devicon-npm-original-wordmark",
                    "label" => "npm"
                ],
                [
                    "value" => "devicon-nuget-original",
                    "label" => "nuget"
                ],
                [
                    "value" => "devicon-numpy-original",
                    "label" => "numpy"
                ],
                [
                    "value" => "devicon-nuxtjs-plain",
                    "label" => "nuxtjs"
                ],
                [
                    "value" => "devicon-objectivec-plain",
                    "label" => "objectivec"
                ],
                [
                    "value" => "devicon-opera-plain",
                    "label" => "opera"
                ],
                [
                    "value" => "devicon-ocaml-plain",
                    "label" => "ocaml"
                ],
                [
                    "value" => "devicon-openal-plain",
                    "label" => "openal"
                ],
                [
                    "value" => "devicon-opengl-plain",
                    "label" => "opengl"
                ],
                [
                    "value" => "devicon-opensuse-plain",
                    "label" => "opensuse"
                ],
                [
                    "value" => "devicon-oracle-original",
                    "label" => "oracle"
                ],
                [
                    "value" => "devicon-pandas-original",
                    "label" => "pandas"
                ],
                [
                    "value" => "devicon-perl-plain",
                    "label" => "perl"
                ],
                [
                    "value" => "devicon-phalcon-plain",
                    "label" => "phalcon"
                ],
                [
                    "value" => "devicon-photoshop-plain",
                    "label" => "photoshop"
                ],
                [
                    "value" => "devicon-php-plain",
                    "label" => "php"
                ],
                [
                    "value" => "devicon-phpstorm-plain",
                    "label" => "phpstorm"
                ],
                [
                    "value" => "devicon-podman-plain",
                    "label" => "podman"
                ],
                [
                    "value" => "devicon-polygon-plain",
                    "label" => "polygon"
                ],
                [
                    "value" => "devicon-postgresql-plain",
                    "label" => "postgresql"
                ],
                [
                    "value" => "devicon-premierepro-plain",
                    "label" => "premierepro"
                ],
                [
                    "value" => "devicon-processing-plain",
                    "label" => "processing"
                ],
                [
                    "value" => "devicon-protractor-plain",
                    "label" => "protractor"
                ],
                [
                    "value" => "devicon-putty-plain",
                    "label" => "putty"
                ],
                [
                    "value" => "devicon-pycharm-plain",
                    "label" => "pycharm"
                ],
                [
                    "value" => "devicon-python-plain",
                    "label" => "python"
                ],
                [
                    "value" => "devicon-pytorch-original",
                    "label" => "pytorch"
                ],
                [
                    "value" => "devicon-raspberrypi-line",
                    "label" => "raspberrypi"
                ],
                [
                    "value" => "devicon-phoenix-plain",
                    "label" => "phoenix"
                ],
                [
                    "value" => "devicon-qt-original",
                    "label" => "qt"
                ],
                [
                    "value" => "devicon-r-original",
                    "label" => "r"
                ],
                [
                    "value" => "devicon-rails-plain",
                    "label" => "rails"
                ],
                [
                    "value" => "devicon-react-original",
                    "label" => "react"
                ],
                [
                    "value" => "devicon-redhat-plain",
                    "label" => "redhat"
                ],
                [
                    "value" => "devicon-redis-plain",
                    "label" => "redis"
                ],
                [
                    "value" => "devicon-redux-original",
                    "label" => "redux"
                ],
                [
                    "value" => "devicon-rocksdb-plain",
                    "label" => "rocksdb"
                ],
                [
                    "value" => "devicon-ruby-plain",
                    "label" => "ruby"
                ],
                [
                    "value" => "devicon-rubymine-plain",
                    "label" => "rubymine"
                ],
                [
                    "value" => "devicon-rust-plain",
                    "label" => "rust"
                ],
                [
                    "value" => "devicon-safari-plain",
                    "label" => "safari"
                ],
                [
                    "value" => "devicon-salesforce-plain",
                    "label" => "salesforce"
                ],
                [
                    "value" => "devicon-sdl-plain",
                    "label" => "sdl"
                ],
                [
                    "value" => "devicon-rstudio-plain",
                    "label" => "rstudio"
                ],
                [
                    "value" => "devicon-sass-original",
                    "label" => "sass"
                ],
                [
                    "value" => "devicon-scala-plain",
                    "label" => "scala"
                ],
                [
                    "value" => "devicon-selenium-original",
                    "label" => "selenium"
                ],
                [
                    "value" => "devicon-sequelize-plain",
                    "label" => "sequelize"
                ],
                [
                    "value" => "devicon-shopware-original",
                    "label" => "shopware"
                ],
                [
                    "value" => "devicon-shotgrid-plain",
                    "label" => "shotgrid"
                ],
                [
                    "value" => "devicon-sketch-line",
                    "label" => "sketch"
                ],
                [
                    "value" => "devicon-slack-plain",
                    "label" => "slack"
                ],
                [
                    "value" => "devicon-socketio-original",
                    "label" => "socketio"
                ],
                [
                    "value" => "devicon-solidity-plain",
                    "label" => "solidity"
                ],
                [
                    "value" => "devicon-sourcetree-original",
                    "label" => "sourcetree"
                ],
                [
                    "value" => "devicon-spring-plain",
                    "label" => "spring"
                ],
                [
                    "value" => "devicon-spss-plain",
                    "label" => "spss"
                ],
                [
                    "value" => "devicon-sqlalchemy-plain",
                    "label" => "sqlalchemy"
                ],
                [
                    "value" => "devicon-sqlite-plain",
                    "label" => "sqlite"
                ],
                [
                    "value" => "devicon-subversion-original",
                    "label" => "subversion"
                ],
                [
                    "value" => "devicon-microsoftsqlserver-plain",
                    "label" => "microsoftsqlserver"
                ],
                [
                    "value" => "devicon-ssh-original",
                    "label" => "ssh"
                ],
                [
                    "value" => "devicon-stylus-original",
                    "label" => "stylus"
                ],
                [
                    "value" => "devicon-svelte-plain",
                    "label" => "svelte"
                ],
                [
                    "value" => "devicon-swift-plain",
                    "label" => "swift"
                ],
                [
                    "value" => "devicon-symfony-original",
                    "label" => "symfony"
                ],
                [
                    "value" => "devicon-storybook-plain",
                    "label" => "storybook"
                ],
                [
                    "value" => "devicon-tailwindcss-original-wordmark",
                    "label" => "tailwindcss"
                ],
                [
                    "value" => "devicon-tensorflow-original",
                    "label" => "tensorflow"
                ],
                [
                    "value" => "devicon-terraform-plain",
                    "label" => "terraform"
                ],
                [
                    "value" => "devicon-threejs-original",
                    "label" => "threejs"
                ],
                [
                    "value" => "devicon-tomcat-line",
                    "label" => "tomcat"
                ],
                [
                    "value" => "devicon-tortoisegit-plain",
                    "label" => "tortoisegit"
                ],
                [
                    "value" => "devicon-towergit-plain",
                    "label" => "towergit"
                ],
                [
                    "value" => "devicon-travis-plain",
                    "label" => "travis"
                ],
                [
                    "value" => "devicon-thealgorithms-plain",
                    "label" => "thealgorithms"
                ],
                [
                    "value" => "devicon-trello-plain",
                    "label" => "trello"
                ],
                [
                    "value" => "devicon-twitter-original",
                    "label" => "twitter"
                ],
                [
                    "value" => "devicon-typescript-plain",
                    "label" => "typescript"
                ],
                [
                    "value" => "devicon-typo3-plain",
                    "label" => "typo3"
                ],
                [
                    "value" => "devicon-ubuntu-plain",
                    "label" => "ubuntu"
                ],
                [
                    "value" => "devicon-unity-original",
                    "label" => "unity"
                ],
                [
                    "value" => "devicon-unix-original",
                    "label" => "unix"
                ],
                [
                    "value" => "devicon-unrealengine-original",
                    "label" => "unrealengine"
                ],
                [
                    "value" => "devicon-uwsgi-plain",
                    "label" => "uwsgi"
                ],
                [
                    "value" => "devicon-vagrant-plain",
                    "label" => "vagrant"
                ],
                [
                    "value" => "devicon-vim-plain",
                    "label" => "vim"
                ],
                [
                    "value" => "devicon-visualstudio-plain",
                    "label" => "visualstudio"
                ],
                [
                    "value" => "devicon-vuejs-plain",
                    "label" => "vuejs"
                ],
                [
                    "value" => "devicon-vuestorefront-plain",
                    "label" => "vuestorefront"
                ],
                [
                    "value" => "devicon-vscode-plain",
                    "label" => "vscode"
                ],
                [
                    "value" => "devicon-webflow-original",
                    "label" => "webflow"
                ],
                [
                    "value" => "devicon-weblate-plain",
                    "label" => "weblate"
                ],
                [
                    "value" => "devicon-webpack-plain",
                    "label" => "webpack"
                ],
                [
                    "value" => "devicon-webstorm-plain",
                    "label" => "webstorm"
                ],
                [
                    "value" => "devicon-windows8-original",
                    "label" => "windows8"
                ],
                [
                    "value" => "devicon-woocommerce-plain",
                    "label" => "woocommerce"
                ],
                [
                    "value" => "devicon-wordpress-plain",
                    "label" => "wordpress"
                ],
                [
                    "value" => "devicon-xamarin-original",
                    "label" => "xamarin"
                ],
                [
                    "value" => "devicon-xcode-plain",
                    "label" => "xcode"
                ],
                [
                    "value" => "devicon-xd-plain",
                    "label" => "xd"
                ],
                [
                    "value" => "devicon-yarn-plain",
                    "label" => "yarn"
                ],
                [
                    "value" => "devicon-yii-plain",
                    "label" => "yii"
                ],
                [
                    "value" => "devicon-yunohost-plain",
                    "label" => "yunohost"
                ],
                [
                    "value" => "devicon-zend-plain",
                    "label" => "zend"
                ],
                [
                    "value" => "devicon-zig-original",
                    "label" => "zig"
                ],
                [
                    "value" => "devicon-pytest-plain",
                    "label" => "pytest"
                ],
                [
                    "value" => "devicon-opencv-plain",
                    "label" => "opencv"
                ],
                [
                    "value" => "devicon-fastapi-plain",
                    "label" => "fastapi"
                ],
                [
                    "value" => "devicon-k3s-original",
                    "label" => "k3s"
                ],
                [
                    "value" => "devicon-packer-original",
                    "label" => "packer"
                ],
                [
                    "value" => "devicon-anaconda-original",
                    "label" => "anaconda"
                ],
                [
                    "value" => "devicon-rspec-original",
                    "label" => "rspec"
                ],
                [
                    "value" => "devicon-argocd-plain",
                    "label" => "argocd"
                ],
                [
                    "value" => "devicon-prometheus-original",
                    "label" => "prometheus"
                ],
                [
                    "value" => "devicon-blender-original",
                    "label" => "blender"
                ],
                [
                    "value" => "devicon-dropwizard-plain",
                    "label" => "dropwizard"
                ],
                [
                    "value" => "devicon-vuetify-line",
                    "label" => "vuetify"
                ],
                [
                    "value" => "devicon-fedora-plain",
                    "label" => "fedora"
                ]
            ]
        ]);
    }

    public function activate(Bridger $bridger): void
    {

    }

    public function uninstall(Bridger $bridger): void
    {

    }

    public function getServices(Bridger $bridger): array
    {
        return [];
    }

    public function provider(Bridger $bridger): ?PluginProviderInterface
    {
        return null;
    }

}
