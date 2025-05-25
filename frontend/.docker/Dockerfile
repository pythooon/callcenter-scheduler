FROM node:20-alpine

WORKDIR /var/www/app

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

ENTRYPOINT ["sh", "-c", "npm install && npm start"]
