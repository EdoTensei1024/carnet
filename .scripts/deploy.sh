set -e

echo "Deployment started ..."

# Pull the latest version of the app
git pull origin master
echo "New changes copied to server !"

echo "Deployment Finished! go to https://carnet.florapare.bf"
