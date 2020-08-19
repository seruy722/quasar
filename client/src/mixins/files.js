export default {
  methods: {
    fileUrl() {
      return process.env.DEV ? 'http://sp.com.ua/storage/' : 'http://servercargo007.net.ua/storage/app/public/';
    },
    downloadFromIndex(index, files) {
      const file = files[index - 1];
      devlog.log('FILE', file);
      const link = document.createElement('a');
      link.href = `${this.fileUrl()}${file.path}`;
      link.setAttribute('download', 'dfs');
      link.setAttribute('target', '_blank');
      document.body.appendChild(link);
      link.click();
    },
    getFileExt({ name }) {
      if (name) {
        return name.slice(name.lastIndexOf('.') + 1);
      }
      return name;
    },
  },
};
