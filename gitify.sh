#!/bin/bash

git init
find * -size +4M -type f -print >> .gitignore
git add -A
git commit -m "first commit"
git branch -M main
git remote add origin https://raychorn:285ea38da2a7fd12ea6048ac797c1147e5874f15@github.com/raychorn/svn_osticket_1.6.rc4.git
git push -u origin main
