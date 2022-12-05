<?php
    namespace codad5\examples\middleware;
    require(__DIR__ . '/../../vendor/autoload.php');
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\Key;
    // use \Firebase\




    class jwtT extends JWT
    {
        public function __construct($key, $name = 'jwt', $expireIn = 6000, $alg = 'HS256', $host = null)
        {
            $this->name = $name;
            // $this->protocol = $_SERVER['SERVER_PROTOCOL'];
            // get server protocol if http or https
            $this->protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $this->url = $host ? $host : $_SERVER['HTTP_HOST'];
            $this->key = $key;
            $this->alg = $alg;
            $this->start = time();
            $this->expireIn = $this->start + $expireIn;
        }

        public function create($req, $res)
        {
            $payload = [
                'iss' => $this->key,
                'aud' => $this->protocol.$this->url
            ];
            $payload['iat'] = $this->start;
            $payload['exp'] = $this->expireIn;
            // $res

            $auth_token =  $this->encode($payload, $this->key, $this->alg);
            $req->append($this->name.'_auth_token', $auth_token);
            // add the auth token to header of response
            header('Authorization: Bearer ' . $auth_token);
            return $auth_token;
        }
        public function verify($req, $res)
        {
            $timestamp = time();
            $auth_token = $req->header('authorization');
            if(!$auth_token) {
                $res->send(['message'=>"auth failed", 'header' => $req->headers()])->status(403);
                exit;
            }
            try {
                $payload = $this->decode($auth_token, new Key($this->key, $this->alg));
            } catch (\Exception $e) {
                $res->send(['message'=>"auth failed","error" => $e->getMessage(), 'header' => $req->headers()])->status(403);
                exit;
            }   

        }
        public function green($req, $res){
            echo "hello world";
            return $res->send($req->headers())->status(200);
        }
    }