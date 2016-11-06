# workspace (In progress)
A library of all classes that are commonly used in my other libraries/projects. This library is intended for my personal use in order to make life a little easier. When I work on this I am only thinking about myself and no one else. That being said, you are welcome to use it on your own.


## Usage

### Configuration

Overwrite the <code>Workspace</code> class in your project and set your PHP config file in <code>Workspace::$configInfo</code>. Default is <code> __DIR__ . '/../config.php'</code>. Once the file path is set you can get a config value like below.

    //This is your overwritten version of Workspace class where you set the config file.
    Workspace::config()->get('config-name-here');
    
### Logger

Overwrite the <code>Workspace</code> class in your project and set your log path in <code>Workspace::$logPath </code>. Default is <code> __DIR__ . '/../storage/logs/</code>. Once the log path is set you get start using the logger like below.

    //This is your overwritten version of Workspace class where you set the config file.
    Workspace::log()->warn('message');
    Workspace::log()->error('message');
    Workspace::log()->info('message');
    Workspace::log()->debug('message');