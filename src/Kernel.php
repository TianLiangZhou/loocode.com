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
            'type' => AbstractControl::SELECT,
            'label'=> '主标签',
            'options' => [
                [
                    "value"=> "adonisjs",
                    "label"=> "adonisjs"
                ],
                [
                    "value"=> "aftereffects",
                    "label"=> "aftereffects"
                ],
                [
                    "value"=> "amazonwebservices",
                    "label"=> "amazonwebservices"
                ],
                [
                    "value"=> "android",
                    "label"=> "android"
                ],
                [
                    "value"=> "androidstudio",
                    "label"=> "androidstudio"
                ],
                [
                    "value"=> "aarch64",
                    "label"=> "aarch64"
                ],
                [
                    "value"=> "angularjs",
                    "label"=> "angularjs"
                ],
                [
                    "value"=> "ansible",
                    "label"=> "ansible"
                ],
                [
                    "value"=> "apache",
                    "label"=> "apache"
                ],
                [
                    "value"=> "apachekafka",
                    "label"=> "apachekafka"
                ],
                [
                    "value"=> "appcelerator",
                    "label"=> "appcelerator"
                ],
                [
                    "value"=> "apple",
                    "label"=> "apple"
                ],
                [
                    "value"=> "appwrite",
                    "label"=> "appwrite"
                ],
                [
                    "value"=> "arduino",
                    "label"=> "arduino"
                ],
                [
                    "value"=> "atom",
                    "label"=> "atom"
                ],
                [
                    "value"=> "azure",
                    "label"=> "azure"
                ],
                [
                    "value"=> "babel",
                    "label"=> "babel"
                ],
                [
                    "value"=> "backbonejs",
                    "label"=> "backbonejs"
                ],
                [
                    "value"=> "bamboo",
                    "label"=> "bamboo"
                ],
                [
                    "value"=> "bash",
                    "label"=> "bash"
                ],
                [
                    "value"=> "behance",
                    "label"=> "behance"
                ],
                [
                    "value"=> "bitbucket",
                    "label"=> "bitbucket"
                ],
                [
                    "value"=> "bootstrap",
                    "label"=> "bootstrap"
                ],
                [
                    "value"=> "bulma",
                    "label"=> "bulma"
                ],
                [
                    "value"=> "bower",
                    "label"=> "bower"
                ],
                [
                    "value"=> "c",
                    "label"=> "c"
                ],
                [
                    "value"=> "cakephp",
                    "label"=> "cakephp"
                ],
                [
                    "value"=> "canva",
                    "label"=> "canva"
                ],
                [
                    "value"=> "centos",
                    "label"=> "centos"
                ],
                [
                    "value"=> "ceylon",
                    "label"=> "ceylon"
                ],
                [
                    "value"=> "chrome",
                    "label"=> "chrome"
                ],
                [
                    "value"=> "circleci",
                    "label"=> "circleci"
                ],
                [
                    "value"=> "clojure",
                    "label"=> "clojure"
                ],
                [
                    "value"=> "cmake",
                    "label"=> "cmake"
                ],
                [
                    "value"=> "clojurescript",
                    "label"=> "clojurescript"
                ],
                [
                    "value"=> "codecov",
                    "label"=> "codecov"
                ],
                [
                    "value"=> "codeigniter",
                    "label"=> "codeigniter"
                ],
                [
                    "value"=> "codepen",
                    "label"=> "codepen"
                ],
                [
                    "value"=> "coffeescript",
                    "label"=> "coffeescript"
                ],
                [
                    "value"=> "composer",
                    "label"=> "composer"
                ],
                [
                    "value"=> "confluence",
                    "label"=> "confluence"
                ],
                [
                    "value"=> "couchdb",
                    "label"=> "couchdb"
                ],
                [
                    "value"=> "cplusplus",
                    "label"=> "cplusplus"
                ],
                [
                    "value"=> "csharp",
                    "label"=> "csharp"
                ],
                [
                    "value"=> "css3",
                    "label"=> "css3"
                ],
                [
                    "value"=> "cucumber",
                    "label"=> "cucumber"
                ],
                [
                    "value"=> "crystal",
                    "label"=> "crystal"
                ],
                [
                    "value"=> "d3js",
                    "label"=> "d3js"
                ],
                [
                    "value"=> "dart",
                    "label"=> "dart"
                ],
                [
                    "value"=> "debian",
                    "label"=> "debian"
                ],
                [
                    "value"=> "denojs",
                    "label"=> "denojs"
                ],
                [
                    "value"=> "devicon",
                    "label"=> "devicon"
                ],
                [
                    "value"=> "django",
                    "label"=> "django"
                ],
                [
                    "value"=> "docker",
                    "label"=> "docker"
                ],
                [
                    "value"=> "doctrine",
                    "label"=> "doctrine"
                ],
                [
                    "value"=> "dot-net",
                    "label"=> "dot-net"
                ],
                [
                    "value"=> "dotnetcore",
                    "label"=> "dotnetcore"
                ],
                [
                    "value"=> "drupal",
                    "label"=> "drupal"
                ],
                [
                    "value"=> "digitalocean",
                    "label"=> "digitalocean"
                ],
                [
                    "value"=> "discordjs",
                    "label"=> "discordjs"
                ],
                [
                    "value"=> "electron",
                    "label"=> "electron"
                ],
                [
                    "value"=> "eleventy",
                    "label"=> "eleventy"
                ],
                [
                    "value"=> "elixir",
                    "label"=> "elixir"
                ],
                [
                    "value"=> "elm",
                    "label"=> "elm"
                ],
                [
                    "value"=> "ember",
                    "label"=> "ember"
                ],
                [
                    "value"=> "embeddedc",
                    "label"=> "embeddedc"
                ],
                [
                    "value"=> "erlang",
                    "label"=> "erlang"
                ],
                [
                    "value"=> "eslint",
                    "label"=> "eslint"
                ],
                [
                    "value"=> "express",
                    "label"=> "express"
                ],
                [
                    "value"=> "facebook",
                    "label"=> "facebook"
                ],
                [
                    "value"=> "feathersjs",
                    "label"=> "feathersjs"
                ],
                [
                    "value"=> "figma",
                    "label"=> "figma"
                ],
                [
                    "value"=> "filezilla",
                    "label"=> "filezilla"
                ],
                [
                    "value"=> "firebase",
                    "label"=> "firebase"
                ],
                [
                    "value"=> "firefox",
                    "label"=> "firefox"
                ],
                [
                    "value"=> "flask",
                    "label"=> "flask"
                ],
                [
                    "value"=> "flutter",
                    "label"=> "flutter"
                ],
                [
                    "value"=> "foundation",
                    "label"=> "foundation"
                ],
                [
                    "value"=> "fsharp",
                    "label"=> "fsharp"
                ],
                [
                    "value"=> "gatling",
                    "label"=> "gatling"
                ],
                [
                    "value"=> "gatsby",
                    "label"=> "gatsby"
                ],
                [
                    "value"=> "rect",
                    "label"=> "rect"
                ],
                [
                    "value"=> "gcc",
                    "label"=> "gcc"
                ],
                [
                    "value"=> "gentoo",
                    "label"=> "gentoo"
                ],
                [
                    "value"=> "gimp",
                    "label"=> "gimp"
                ],
                [
                    "value"=> "git",
                    "label"=> "git"
                ],
                [
                    "value"=> "github",
                    "label"=> "github"
                ],
                [
                    "value"=> "gitlab",
                    "label"=> "gitlab"
                ],
                [
                    "value"=> "gitter",
                    "label"=> "gitter"
                ],
                [
                    "value"=> "go",
                    "label"=> "go"
                ],
                [
                    "value"=> "google",
                    "label"=> "google"
                ],
                [
                    "value"=> "googlecloud",
                    "label"=> "googlecloud"
                ],
                [
                    "value"=> "gradle",
                    "label"=> "gradle"
                ],
                [
                    "value"=> "grafana",
                    "label"=> "grafana"
                ],
                [
                    "value"=> "grails",
                    "label"=> "grails"
                ],
                [
                    "value"=> "graphql",
                    "label"=> "graphql"
                ],
                [
                    "value"=> "groovy",
                    "label"=> "groovy"
                ],
                [
                    "value"=> "grunt",
                    "label"=> "grunt"
                ],
                [
                    "value"=> "gulp",
                    "label"=> "gulp"
                ],
                [
                    "value"=> "godot",
                    "label"=> "godot"
                ],
                [
                    "value"=> "haskell",
                    "label"=> "haskell"
                ],
                [
                    "value"=> "handlebars",
                    "label"=> "handlebars"
                ],
                [
                    "value"=> "haxe",
                    "label"=> "haxe"
                ],
                [
                    "value"=> "heroku",
                    "label"=> "heroku"
                ],
                [
                    "value"=> "html5",
                    "label"=> "html5"
                ],
                [
                    "value"=> "hugo",
                    "label"=> "hugo"
                ],
                [
                    "value"=> "ie10",
                    "label"=> "ie10"
                ],
                [
                    "value"=> "ifttt",
                    "label"=> "ifttt"
                ],
                [
                    "value"=> "illustrator",
                    "label"=> "illustrator"
                ],
                [
                    "value"=> "inkscape",
                    "label"=> "inkscape"
                ],
                [
                    "value"=> "intellij",
                    "label"=> "intellij"
                ],
                [
                    "value"=> "ionic",
                    "label"=> "ionic"
                ],
                [
                    "value"=> "jamstack",
                    "label"=> "jamstack"
                ],
                [
                    "value"=> "jasmine",
                    "label"=> "jasmine"
                ],
                [
                    "value"=> "java",
                    "label"=> "java"
                ],
                [
                    "value"=> "javascript",
                    "label"=> "javascript"
                ],
                [
                    "value"=> "jeet",
                    "label"=> "jeet"
                ],
                [
                    "value"=> "jest",
                    "label"=> "jest"
                ],
                [
                    "value"=> "jenkins",
                    "label"=> "jenkins"
                ],
                [
                    "value"=> "jetbrains",
                    "label"=> "jetbrains"
                ],
                [
                    "value"=> "jira",
                    "label"=> "jira"
                ],
                [
                    "value"=> "jquery",
                    "label"=> "jquery"
                ],
                [
                    "value"=> "julia",
                    "label"=> "julia"
                ],
                [
                    "value"=> "jupyter",
                    "label"=> "jupyter"
                ],
                [
                    "value"=> "kaggle",
                    "label"=> "kaggle"
                ],
                [
                    "value"=> "karma",
                    "label"=> "karma"
                ],
                [
                    "value"=> "kotlin",
                    "label"=> "kotlin"
                ],
                [
                    "value"=> "knockout",
                    "label"=> "knockout"
                ],
                [
                    "value"=> "krakenjs",
                    "label"=> "krakenjs"
                ],
                [
                    "value"=> "kubernetes",
                    "label"=> "kubernetes"
                ],
                [
                    "value"=> "labview",
                    "label"=> "labview"
                ],
                [
                    "value"=> "laravel",
                    "label"=> "laravel"
                ],
                [
                    "value"=> "latex",
                    "label"=> "latex"
                ],
                [
                    "value"=> "less",
                    "label"=> "less"
                ],
                [
                    "value"=> "linkedin",
                    "label"=> "linkedin"
                ],
                [
                    "value"=> "lua",
                    "label"=> "lua"
                ],
                [
                    "value"=> "linux",
                    "label"=> "linux"
                ],
                [
                    "value"=> "materialui",
                    "label"=> "materialui"
                ],
                [
                    "value"=> "matlab",
                    "label"=> "matlab"
                ],
                [
                    "value"=> "magento",
                    "label"=> "magento"
                ],
                [
                    "value"=> "markdown",
                    "label"=> "markdown"
                ],
                [
                    "value"=> "maya",
                    "label"=> "maya"
                ],
                [
                    "value"=> "meteor",
                    "label"=> "meteor"
                ],
                [
                    "value"=> "minitab",
                    "label"=> "minitab"
                ],
                [
                    "value"=> "mocha",
                    "label"=> "mocha"
                ],
                [
                    "value"=> "modx",
                    "label"=> "modx"
                ],
                [
                    "value"=> "mongodb",
                    "label"=> "mongodb"
                ],
                [
                    "value"=> "moodle",
                    "label"=> "moodle"
                ],
                [
                    "value"=> "msdos",
                    "label"=> "msdos"
                ],
                [
                    "value"=> "mysql",
                    "label"=> "mysql"
                ],
                [
                    "value"=> "neo4j",
                    "label"=> "neo4j"
                ],
                [
                    "value"=> "nestjs",
                    "label"=> "nestjs"
                ],
                [
                    "value"=> "networkx",
                    "label"=> "networkx"
                ],
                [
                    "value"=> "nextjs",
                    "label"=> "nextjs"
                ],
                [
                    "value"=> "nginx",
                    "label"=> "nginx"
                ],
                [
                    "value"=> "nixos",
                    "label"=> "nixos"
                ],
                [
                    "value"=> "nodejs",
                    "label"=> "nodejs"
                ],
                [
                    "value"=> "nodewebkit",
                    "label"=> "nodewebkit"
                ],
                [
                    "value"=> "npm",
                    "label"=> "npm"
                ],
                [
                    "value"=> "nuget",
                    "label"=> "nuget"
                ],
                [
                    "value"=> "numpy",
                    "label"=> "numpy"
                ],
                [
                    "value"=> "nuxtjs",
                    "label"=> "nuxtjs"
                ],
                [
                    "value"=> "objectivec",
                    "label"=> "objectivec"
                ],
                [
                    "value"=> "opera",
                    "label"=> "opera"
                ],
                [
                    "value"=> "ocaml",
                    "label"=> "ocaml"
                ],
                [
                    "value"=> "openal",
                    "label"=> "openal"
                ],
                [
                    "value"=> "opengl",
                    "label"=> "opengl"
                ],
                [
                    "value"=> "opensuse",
                    "label"=> "opensuse"
                ],
                [
                    "value"=> "oracle",
                    "label"=> "oracle"
                ],
                [
                    "value"=> "pandas",
                    "label"=> "pandas"
                ],
                [
                    "value"=> "perl",
                    "label"=> "perl"
                ],
                [
                    "value"=> "phalcon",
                    "label"=> "phalcon"
                ],
                [
                    "value"=> "photoshop",
                    "label"=> "photoshop"
                ],
                [
                    "value"=> "php",
                    "label"=> "php"
                ],
                [
                    "value"=> "phpstorm",
                    "label"=> "phpstorm"
                ],
                [
                    "value"=> "podman",
                    "label"=> "podman"
                ],
                [
                    "value"=> "polygon",
                    "label"=> "polygon"
                ],
                [
                    "value"=> "postgresql",
                    "label"=> "postgresql"
                ],
                [
                    "value"=> "premierepro",
                    "label"=> "premierepro"
                ],
                [
                    "value"=> "processing",
                    "label"=> "processing"
                ],
                [
                    "value"=> "protractor",
                    "label"=> "protractor"
                ],
                [
                    "value"=> "putty",
                    "label"=> "putty"
                ],
                [
                    "value"=> "pycharm",
                    "label"=> "pycharm"
                ],
                [
                    "value"=> "python",
                    "label"=> "python"
                ],
                [
                    "value"=> "pytorch",
                    "label"=> "pytorch"
                ],
                [
                    "value"=> "raspberrypi",
                    "label"=> "raspberrypi"
                ],
                [
                    "value"=> "phoenix",
                    "label"=> "phoenix"
                ],
                [
                    "value"=> "qt",
                    "label"=> "qt"
                ],
                [
                    "value"=> "r",
                    "label"=> "r"
                ],
                [
                    "value"=> "rails",
                    "label"=> "rails"
                ],
                [
                    "value"=> "react",
                    "label"=> "react"
                ],
                [
                    "value"=> "redhat",
                    "label"=> "redhat"
                ],
                [
                    "value"=> "redis",
                    "label"=> "redis"
                ],
                [
                    "value"=> "redux",
                    "label"=> "redux"
                ],
                [
                    "value"=> "rocksdb",
                    "label"=> "rocksdb"
                ],
                [
                    "value"=> "ruby",
                    "label"=> "ruby"
                ],
                [
                    "value"=> "rubymine",
                    "label"=> "rubymine"
                ],
                [
                    "value"=> "rust",
                    "label"=> "rust"
                ],
                [
                    "value"=> "safari",
                    "label"=> "safari"
                ],
                [
                    "value"=> "salesforce",
                    "label"=> "salesforce"
                ],
                [
                    "value"=> "sdl",
                    "label"=> "sdl"
                ],
                [
                    "value"=> "rstudio",
                    "label"=> "rstudio"
                ],
                [
                    "value"=> "sass",
                    "label"=> "sass"
                ],
                [
                    "value"=> "scala",
                    "label"=> "scala"
                ],
                [
                    "value"=> "selenium",
                    "label"=> "selenium"
                ],
                [
                    "value"=> "sequelize",
                    "label"=> "sequelize"
                ],
                [
                    "value"=> "shopware",
                    "label"=> "shopware"
                ],
                [
                    "value"=> "shotgrid",
                    "label"=> "shotgrid"
                ],
                [
                    "value"=> "sketch",
                    "label"=> "sketch"
                ],
                [
                    "value"=> "slack",
                    "label"=> "slack"
                ],
                [
                    "value"=> "socketio",
                    "label"=> "socketio"
                ],
                [
                    "value"=> "solidity",
                    "label"=> "solidity"
                ],
                [
                    "value"=> "sourcetree",
                    "label"=> "sourcetree"
                ],
                [
                    "value"=> "spring",
                    "label"=> "spring"
                ],
                [
                    "value"=> "spss",
                    "label"=> "spss"
                ],
                [
                    "value"=> "sqlalchemy",
                    "label"=> "sqlalchemy"
                ],
                [
                    "value"=> "sqlite",
                    "label"=> "sqlite"
                ],
                [
                    "value"=> "subversion",
                    "label"=> "subversion"
                ],
                [
                    "value"=> "microsoftsqlserver",
                    "label"=> "microsoftsqlserver"
                ],
                [
                    "value"=> "ssh",
                    "label"=> "ssh"
                ],
                [
                    "value"=> "stylus",
                    "label"=> "stylus"
                ],
                [
                    "value"=> "svelte",
                    "label"=> "svelte"
                ],
                [
                    "value"=> "swift",
                    "label"=> "swift"
                ],
                [
                    "value"=> "symfony",
                    "label"=> "symfony"
                ],
                [
                    "value"=> "storybook",
                    "label"=> "storybook"
                ],
                [
                    "value"=> "tailwindcss",
                    "label"=> "tailwindcss"
                ],
                [
                    "value"=> "tensorflow",
                    "label"=> "tensorflow"
                ],
                [
                    "value"=> "terraform",
                    "label"=> "terraform"
                ],
                [
                    "value"=> "threejs",
                    "label"=> "threejs"
                ],
                [
                    "value"=> "tomcat",
                    "label"=> "tomcat"
                ],
                [
                    "value"=> "tortoisegit",
                    "label"=> "tortoisegit"
                ],
                [
                    "value"=> "towergit",
                    "label"=> "towergit"
                ],
                [
                    "value"=> "travis",
                    "label"=> "travis"
                ],
                [
                    "value"=> "thealgorithms",
                    "label"=> "thealgorithms"
                ],
                [
                    "value"=> "trello",
                    "label"=> "trello"
                ],
                [
                    "value"=> "twitter",
                    "label"=> "twitter"
                ],
                [
                    "value"=> "typescript",
                    "label"=> "typescript"
                ],
                [
                    "value"=> "typo3",
                    "label"=> "typo3"
                ],
                [
                    "value"=> "ubuntu",
                    "label"=> "ubuntu"
                ],
                [
                    "value"=> "unity",
                    "label"=> "unity"
                ],
                [
                    "value"=> "unix",
                    "label"=> "unix"
                ],
                [
                    "value"=> "unrealengine",
                    "label"=> "unrealengine"
                ],
                [
                    "value"=> "uwsgi",
                    "label"=> "uwsgi"
                ],
                [
                    "value"=> "vagrant",
                    "label"=> "vagrant"
                ],
                [
                    "value"=> "vim",
                    "label"=> "vim"
                ],
                [
                    "value"=> "visualstudio",
                    "label"=> "visualstudio"
                ],
                [
                    "value"=> "vuejs",
                    "label"=> "vuejs"
                ],
                [
                    "value"=> "vuestorefront",
                    "label"=> "vuestorefront"
                ],
                [
                    "value"=> "vscode",
                    "label"=> "vscode"
                ],
                [
                    "value"=> "webflow",
                    "label"=> "webflow"
                ],
                [
                    "value"=> "weblate",
                    "label"=> "weblate"
                ],
                [
                    "value"=> "webpack",
                    "label"=> "webpack"
                ],
                [
                    "value"=> "webstorm",
                    "label"=> "webstorm"
                ],
                [
                    "value"=> "windows8",
                    "label"=> "windows8"
                ],
                [
                    "value"=> "woocommerce",
                    "label"=> "woocommerce"
                ],
                [
                    "value"=> "wordpress",
                    "label"=> "wordpress"
                ],
                [
                    "value"=> "xamarin",
                    "label"=> "xamarin"
                ],
                [
                    "value"=> "xcode",
                    "label"=> "xcode"
                ],
                [
                    "value"=> "xd",
                    "label"=> "xd"
                ],
                [
                    "value"=> "yarn",
                    "label"=> "yarn"
                ],
                [
                    "value"=> "yii",
                    "label"=> "yii"
                ],
                [
                    "value"=> "yunohost",
                    "label"=> "yunohost"
                ],
                [
                    "value"=> "zend",
                    "label"=> "zend"
                ],
                [
                    "value"=> "zig",
                    "label"=> "zig"
                ],
                [
                    "value"=> "pytest",
                    "label"=> "pytest"
                ],
                [
                    "value"=> "opencv",
                    "label"=> "opencv"
                ],
                [
                    "value"=> "fastapi",
                    "label"=> "fastapi"
                ],
                [
                    "value"=> "k3s",
                    "label"=> "k3s"
                ],
                [
                    "value"=> "packer",
                    "label"=> "packer"
                ],
                [
                    "value"=> "anaconda",
                    "label"=> "anaconda"
                ],
                [
                    "value"=> "rspec",
                    "label"=> "rspec"
                ],
                [
                    "value"=> "argocd",
                    "label"=> "argocd"
                ],
                [
                    "value"=> "prometheus",
                    "label"=> "prometheus"
                ],
                [
                    "value"=> "blender",
                    "label"=> "blender"
                ],
                [
                    "value"=> "dropwizard",
                    "label"=> "dropwizard"
                ],
                [
                    "value"=> "vuetify",
                    "label"=> "vuetify"
                ],
                [
                    "value"=> "fedora",
                    "label"=> "fedora"
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
