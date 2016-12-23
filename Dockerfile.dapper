FROM node:6.9

RUN npm install -g gulp

WORKDIR /source
COPY package.json .
RUN npm install

ENTRYPOINT ["gulp"]
CMD ["default"]
