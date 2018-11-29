create or replace function f_iniciar_sesion {

p_email varchar,
p_clave varchar

}RETURNS character as 
$BODY$

	begin

		begin

		exception
				when others then
					raise exception '%', SQLERRM;

		end;

		return '';
	
	end;
	
$BODY$
language plpgsql;