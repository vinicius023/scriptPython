import subprocess
import re

def getVersion():
    try:
        with  open(patch+'/trackerup-api/index.php','rb') as fp:
        #lines = fp.readlines()
            for line in fp:
                line=str(line).replace("\\r\\n","")
                if line.find("DEFINE('API_VERSION'") > 0:
                    version = line.split(',')[1].replace('\'','').replace(')','').replace(';','').replace('\n','').split('.')
                    break
    except:
        print('fnf_error')            
    finally:
        fp.close()
        return parseVersionToInt(version)

def setVersion(version, oldVersion):
    try:
        versionStr = str(version[0])+'.'+str(version[1])+'.'+str(version[2])
        oldVersionStr = str(oldVersion[0])+'.'+str(oldVersion[1])+'.'+str(oldVersion[2])
        fp = open('src/index.php','r')
        fileStr = fp.read()
    finally:
        fp.close()
        file_string = (re.sub(r'\sDEFINE\(\'API_VERSION\',\''+oldVersionStr, '\nDEFINE(\'API_VERSION\',\''+versionStr, fileStr))
    
    try:
        fp = open('src/index.php','w')
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
    return [int(value.replace('"', '')) for value in version]

def push(version, branch):
    versionStr = str(version[0])+'.'+str(version[1])+'.'+str(version[2])
    pr = branch.split('/')[1]
    add = 'git add src/index.php'
    commit = 'git commit -m"#' + pr + ' change project version ' + versionStr + '"'
    push = 'git push'
    subprocess.run(add)
    subprocess.run(commit)
    subprocess.run(push)

def main():
    global patch 
    patch = input('Insert tracker patch: ')

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

main()