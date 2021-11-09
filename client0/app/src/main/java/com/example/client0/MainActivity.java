package com.example.client0;

import android.content.Intent;
import android.os.AsyncTask;
import android.support.design.widget.TextInputEditText;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

import java.io.IOException;

import com.example.client0.data.server;
import com.example.client0.LoginActivity;

public class MainActivity extends AppCompatActivity {

    public Button button;
    public TextInputEditText serverIP;


    String php;
    String param_name = null;
    String param = null;
    String answerServer = "Подключение...";



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        button = findViewById(R.id.button);
        serverIP = findViewById(R.id.serverIP);

        button.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View view) {
                if(serverIP.getText().toString().equals(""))
                    Toast.makeText(MainActivity.this, R.string.emptyIP, Toast.LENGTH_SHORT).show();
                else{
                    php = "test";
                    server.server_adr = "http://" + serverIP.getText() + "/";
                    new MyAsyncTask().execute();
                    if(answerServer.trim().equals("Done")) {
                        Toast.makeText(MainActivity.this, "Подключено", Toast.LENGTH_SHORT).show();
                        Intent i;
                        i = new Intent(this, LoginActivity.class);
                        startActivity(i);
                    }
                    else if(answerServer.equals("Подключение..."))
                        Toast.makeText(MainActivity.this, "Не подключено", Toast.LENGTH_SHORT).show();
                    else
                        Toast.makeText(MainActivity.this, "Не подключено: " + answerServer, Toast.LENGTH_SHORT).show();
                }
            }
        });
    }


    class MyAsyncTask extends AsyncTask<String, String, String> {

        @Override
        protected void onPreExecute() {
            //что делать до запроса
            //answerServer = "start";
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            HttpClient httpclient = new DefaultHttpClient();

            HttpGet httpget = new HttpGet(server.server_adr + php + ".php" + "?" + param_name + "=" + param);

            try {
                HttpResponse response = httpclient.execute(httpget);

                if (response.getStatusLine().getStatusCode() == 200) {
                    HttpEntity entity = response.getEntity();
                    answerServer = EntityUtils.toString(entity);
                }else{
                    answerServer = "Something wrong";
                }
            }
            catch (ClientProtocolException e) {
                answerServer = e.toString();
            }
            catch (IOException e) {
                answerServer = e.toString();
            }

            return null;
        }

        @Override
        protected void onPostExecute(String result) {
            // После удачного запроса что с ответом
            super.onPostExecute(result);
        }
    }
}