Pequeno código que fiz para autenticar no Ant Media Server Enterprise e obter o número de streams em execução, em todos os aplicativos criados, mas pode ser alterado para outras APIs do ManagementRestService. Sinta-se a vontade para adaptar como preferir.

# Explicação

O Ant Media Server possui uma API que precisa autenticar antes de usar qualquer recurso geral (*ManagementRestService*), porém, não existe um token e sim é controlado por Session (nível navegador), o que inviabiliza o uso em scripts, como PHP por exemplo. Assim precisa autenticar e usar o session gerado pelo Java/Tomcat, passando como cookie na requisição da API.

Este código também armazena o session em um arquivo *session.txt* para evitar abrir um novo session a cada execução, o que sobrecarrega a memória interna do Java (*JVM Heap*). Se o AMS for reiniciado ou a sessão expirar, ele detecta e gera uma nova sessão e tenta 2 vezes antes de abortar.

# Usando

- altere o servidor, email, senha e a API que irá usar
- da parte onde está "Específico" para frente, altere conforme sua necessidade

# Retorno

- retorna ERR em caso de erro de conexão, autenticação ou obtenção do retorno da API executada
- no caso do applications-info, retorna o total de streamings executando no momento

Fiz o código para parar o serviço do AMS quando ficar ocioso por muito tempo (evitando a cobrança horária da licença mesmo com o servidor ocioso - a cobrança é por hora do *software em execução* por instância (servidor) e não se tem ou não transmissões em curso), por exemplo, se retornar "0" por 30 minutos em seguida, um script Bash pára o serviço. Quando eu vou usar efetivamente, é só reexecutar o serviço.

# Referências

https://antmedia.io/rest/

https://antmedia.io/rest/#/ManagementRestService/authenticateUser

https://antmedia.io/rest/#/ManagementRestService/getApplicationInfo

https://antmedia.io/hourly/

# Café? Eu também gosto!

Meu código te ajudou de alguma forma? Que tal me pagar um cafezinho de R$ 5,00 no na minha chave Pix: pix@arvy.com.br ;)
