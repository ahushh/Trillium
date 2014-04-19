var TermHelp = {
    board: {
        summary: 'Boards management',
        help:
        'Boards management<br />' +
        'Usage: board -option [name]<br />' +
        '<table>' +
        '<tr><td>-l</td><td>Show list</td></tr>' +
        '<tr><td>-c</td><td>Create a board</td></tr>' +
        '<tr><td>-u &lt;board&gt;</td><td>Update a board</td></tr>' +
        '<tr><td>-r &lt;board&gt;</td><td>Remove a board</td></tr>' +
        '<tr><td>-i &lt;board&gt;</td><td>Show information about board</td></tr>' +
        '</table>'
    },
    cd: {
        summary: 'Go to board/thread',
        help:
        'Go to board/thread<br />' +
        'Usage: cd &lt;board&gt;[/thread]'
    },
    login: {
        summary: 'Login into terminal',
        help: 'Login into terminal'
    },
    logout: {
        summary: 'Logout',
        help: 'Logout'
    },
    ls: {
        summary: 'List contents',
        help: 'List contents'
    },
    mkthread: {
        summary: 'Create a thread',
        help:
        'Create a thread<br />' +
        'Usage: mkthread [board] [-option]...<br />' +
        'Options:<br /><table>' +
        '<tr><td>-f</td><td>Attach a file</td></tr>' +
        '</table>'
    },
    mvthread: {
        summary: 'Rename a thread',
        help:
        'Rename a thread<br />' +
        'Usage: mvthread &lt;thread&gt;'
    },
    reply: {
        summary: 'Reply to the thread',
        help:
        'Reply to the thread<br />' +
        'Usage: reply [thread] [-option]...<br />' +
        'Options:<br /><table>' +
        '<tr><td>-f</td><td>Attach a file</td></tr>' +
        '</table>'
    },
    rmimage: {
        summary: 'Remove an image',
        help:
        'Remove an image<br />' +
        'Usage: rmimage &lt;post&gt;'
    },
    rmpost: {
        summary: 'Remove a post',
        help:
        'Remove a post<br />' +
        'Usage: rmpost &lt;post&gt;'
    },
    rmthread: {
        summary: 'Remove a thread',
        help:
        'Remove a thread<br />' +
        'Usage: rmthread &lt;thread&gt;'
    },
    settings: {
        summary: 'User settings',
        help:
        'User settings<br />' +
        'Usage: settings [command] [[key] [value]]...<br />' +
        'Available commands:<br />' +
        'set - Sets a new value for given key<br />' +
        'Example: settings set skin default'
    },
    skins: {
        summary: 'Show available skins',
        help: 'Show available skins'
    },
    user: {
        summary: 'Users management',
        help:
        'Users management<br/>' +
        'Usage: user -option [username] [role]...<br/>' +
        'Available options:<table>' +
        '<tr><td>-c</td><td>Create an user</td></tr>' +
        '<tr><td>-r username role...</td><td>Update roles for an user</td></tr>' +
        '<tr><td>-p [username]</td><td>Update password for an user</td></tr>' +
        '<tr><td>-d username</td><td>Delete an user</td></tr>' +
        '<tr><td>-l</td><td>Show list of users</td></tr>' +
        '</table>'
    }
};