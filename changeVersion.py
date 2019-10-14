import os

# bashCommand = "git add ."
# os.system(bashCommand)


try:
    fp = open('src/index.php','r')
    lines = fp.readlines()
    for line in lines:
        if "DEFINE('API_VERSION'" in line: 
            version = line.split(',')[1].replace('\'','').replace(')','').replace(';','').replace('\n','').split('.')
            break
    print(version)
finally:
    fp.close()