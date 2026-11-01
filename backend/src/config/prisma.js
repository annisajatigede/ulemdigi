const { PrismaClient } = require('@prisma/client');

// Allow BigInt to be converted cleanly to Number in JSON responses
BigInt.prototype.toJSON = function () {
  const num = Number(this);
  return Number.isSafeInteger(num) ? num : this.toString();
};

const prisma = new PrismaClient();

module.exports = prisma;
