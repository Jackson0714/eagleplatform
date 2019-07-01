init() {
	echo "======================================================"
	echo "Init project"
	echo "======================================================"
	docker pull wlyg/builder
	docker pull wlyg/mongo
	docker pull wlyg/php
	docker pull wlyg/nginx
	echo "127.0.0.1 h5.dev.com" >> /etc/hosts
	echo "127.0.0.1 api.dev.com" >> /etc/hosts
	echo "127.0.0.1 frontend.dev.com" >> /etc/hosts
	echo "======================================================"
	echo "Init success. You can run: ./start.sh up"
	echo "======================================================"
}
up() {
	echo "======================================================"
	echo "Start server"
	echo "======================================================"
	cd $PWD/docker
	docker-compose up -d
	echo "======================================================"
	echo "Start server success"
	echo "======================================================"
}
stop() {
	echo "======================================================"
	echo "Stop server"
	echo "======================================================"
	cd $PWD/docker
	docker-compose stop
	echo "======================================================"
	echo "Stop server success"
	echo "======================================================"
}
build() {
	echo "======================================================"
	echo "You can use composer and npm"
	echo "======================================================"
	docker run -it -v $PWD/backend:/srv/backend -v $PWD/frontend:/srv/frontend -v $PWD/h5:/srv/h5 wlyg/builder bash
}

case "$1" in
	init)
		init
		;;
	up)
		up
		;;
	stop)
		stop
		;;
	build)
		build
		;;
	*)
		echo "======================================================"
		echo "Please use init,up,stop,build"
		echo "======================================================"
		;;
esac
