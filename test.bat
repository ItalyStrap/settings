@ECHO OFF
codecept run unit && codecept run wpunit && codecept run functional && codecept run acceptance
