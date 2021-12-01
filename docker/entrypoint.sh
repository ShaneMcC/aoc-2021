#!/bin/bash

DAY="0"
FILE="run.php"
TIME="0";
JIT="0";

while [ "${1}" != "" ]; do
	case "${1}" in
		--file)
			shift
			FILE="${1}"
			;;
		--time)
			TIME="1"
			;;
		--jit)
			JIT="1"
			;;
		*)
			DAY="${1}"
			shift
			break;
			;;
	esac
	shift
done

if [ "${DAY}" == "test" ]; then
	/code/test.sh
	exit ${?}
fi;

if ! [[ "${DAY}" =~ ^[0-9]+$ ]]; then
	echo 'Invalid Day: '${DAY};
	exit 1;
fi;

if [ ! -e "/code/${DAY}/run.php" ]; then
	echo 'Unknown Day: '${DAY};
	exit 1;
fi;

if [ ! -e "/code/${DAY}/${FILE}" ]; then
	echo 'Unknown File: '${FILE};
	exit 1;
fi;

PHPCONFDIR="/etc/php*/conf.d/"
if [ "${JIT}" = "1" ]; then
	echo "opcache.enable_cli=1" > "${PHPCONFDIR}/01_jit.ini"
	echo "opcache.jit_buffer_size=50M" >> "${PHPCONFDIR}/01_jit.ini"
	echo "opcache.jit=tracing" >> "${PHPCONFDIR}/01_jit.ini"
else
	echo "" > "${PHPCONFDIR}/01_jit.ini"
fi;

if [ "${TIME}" = "1" ]; then
	export TIMED=1
	time php /code/${DAY}/${FILE} ${@}
else
	php /code/${DAY}/${FILE} ${@}
fi;
