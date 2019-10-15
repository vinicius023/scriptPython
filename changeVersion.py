import subprocess

# bashCommand = "git add ."
# os.system(bashCommand)

def getVersion():
    try:
        fp = open('src/index.php','r')
        lines = fp.readlines()
        for line in lines:
            if "DEFINE('API_VERSION'" in line: 
                version = line.split(',')[1].replace('\'','').replace(')','').replace(';','').replace('\n','').split('.')
                break
    finally:
        fp.close()
        return version

def getBranch():
    command = 'git rev-parse --abbrev-ref HEAD'
    return subprocess.check_output(command).decode('utf-8')
    
def checkout(branch):
    print('Changing branch...')
    command = 'git checkout ' + branch
    subprocess.run(command)

def compareVersion(versions):
    changed = False
    lastVersion = versions[0]
    #copare first position of version number
    if (versions[1][0] > lastVersion[0]):
        changed = True
        lastVersion = versions[1]
    if (versions[2][0] > lastVersion[0]):
        changed = True
        lastVersion = versions[2]

    #copare second position of version number
    if not changed:
        if (versions[1][1] > lastVersion[1]):
            changed = True
            lastVersion = versions[1]
        if (versions[2][1] > lastVersion[1]):
            changed = True
            lastVersion = versions[2]

    #copare third position of version number
    if not changed:
        if (versions[1][2] > lastVersion[2]):
            lastVersion = versions[1]
        if (versions[2][2] > lastVersion[2]):
            lastVersion = versions[2]

    return lastVersion

def main():
    task = getBranch()
    versionTask = getVersion()

    checkout('dev')
    versionDev = getVersion()

    checkout('master')
    versionMaster = getVersion()

    checkout(task)

    print(compareVersion([versionTask, versionDev, versionMaster]))

main()