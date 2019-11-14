import subprocess
import re
import sys

def getVersion():
    try:
        fp = open(path+'index.php','rb')
        lines = fp.readlines()
        for line in lines:
            decodeLine = line.decode('utf-8')
            if "DEFINE('API_VERSION'" in decodeLine:
                version = decodeLine.split(',')[1].replace('\'','').replace(')','').replace(';','').replace('\n','').split('.')
                break
    except:
        print('Error on read file')
    finally:
        fp.close()
        return parseVersionToInt(version)

def setVersion(version, oldVersion):
    try:
        versionStr = str(version[0])+'.'+str(version[1])+'.'+str(version[2])
        oldVersionStr = str(oldVersion[0])+'.'+str(oldVersion[1])+'.'+str(oldVersion[2])
        fp = open(path+'index.php','rb')
        fileStr = fp.read().split('\r\n')
        fileStr = fileStr.decode('utf-8')
    finally:
        fp.close()
        file_string = (re.sub(r'\sDEFINE\(\'API_VERSION\',\''+oldVersionStr, '\nDEFINE(\'API_VERSION\',\''+versionStr, fileStr))
    
    try:
        fp = open(path+'index.php','w', encoding="utf-8")
        fp.write(file_string)
    finally:
        fp.close()

def getBranch():
    command = 'git rev-parse --abbrev-ref HEAD'
    return subprocess.check_output(command).decode('utf-8').replace('\n','')
    
def pull():
    command = 'git pull'
    subprocess.run(command)

def checkout(branch):
    print('\nChanging branch...')
    command = 'git checkout ' + branch
    subprocess.run(command)

def compareVersion(versions):
    lastVersion = versions[0]

    #copare second position of version number
    if (versions[1][1] > lastVersion[1]):
        lastVersion = versions[1]
    else:
        #copare third position of version number
        if (versions[1][2] >= lastVersion[2]):
            lastVersion = versions[1]

    return lastVersion

def parseVersionToInt(version):
    return [int(value) for value in version]

def push(version, branch):
    versionStr = str(version[0])+'.'+str(version[1])+'.'+str(version[2])
    pr = branch.split('/')[1]
    add = 'git add '+path+'index.php'
    commit = 'git commit -m"#'+pr+' change project version '+versionStr+'"'
    push = 'git push'
    subprocess.run(add)
    subprocess.run(commit)
    subprocess.run(push)

def main(argv):
    global path
    path = argv[1]

    task = getBranch()
    versionTask = getVersion()

    checkout('dev')
    pull()
    versionDev = getVersion()

    checkout(task)

    lastVersion = compareVersion([versionTask, versionDev])
    lastVersion[2] += 1

    setVersion(lastVersion, versionTask)

    push(lastVersion, task)

if __name__ == "__main__":
    main(sys.argv[0:])